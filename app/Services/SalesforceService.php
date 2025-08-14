<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SalesforceService
{
    protected $instanceUrl;
    protected $accessToken;

    public function __construct()
    {
        $this->instanceUrl = rtrim(env('SF_INSTANCE_URL'), '/');
        $this->accessToken = env('SF_ACCESS_TOKEN');
    }

    /**
     * Create a Lead in Salesforce
     */
    public function createLead(array $data)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->accessToken}",
            'Content-Type'  => 'application/json'
        ])->post("{$this->instanceUrl}/services/data/v64.0/sobjects/Lead", $data);

        if ($response->successful()) {
            return [
                'status'  => 'success',
                'data'    => $response->json()
            ];
        }

        return [
            'status'  => 'error',
            'message' => $response->body()
        ];
    }

    /**
     * Upload file to Salesforce Lead as ContentDocument
     */
    public function uploadFileToLead($leadId, $file, $title, $description)
    {
        // Step 1: Create ContentVersion (the file)
        $fileContent = base64_encode(file_get_contents($file->path()));
        
        $contentVersionData = [
            'Title' => $title,
            'Description' => $description,
            'PathOnClient' => $file->getClientOriginalName(),
            'VersionData' => $fileContent
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->accessToken}",
            'Content-Type'  => 'application/json'
        ])->post("{$this->instanceUrl}/services/data/v64.0/sobjects/ContentVersion", $contentVersionData);

        if (!$response->successful()) {
            throw new \Exception('Failed to upload file: ' . $response->body());
        }

        $contentVersionResult = $response->json();
        $contentVersionId = $contentVersionResult['id'];

        // Step 2: Get ContentDocumentId from ContentVersion
        $contentVersionResponse = Http::withHeaders([
            'Authorization' => "Bearer {$this->accessToken}"
        ])->get("{$this->instanceUrl}/services/data/v64.0/sobjects/ContentVersion/{$contentVersionId}");

        if (!$contentVersionResponse->successful()) {
            throw new \Exception('Failed to get ContentDocument ID: ' . $contentVersionResponse->body());
        }

        $contentDocumentId = $contentVersionResponse->json()['ContentDocumentId'];

        // Step 3: Link ContentDocument to Lead using ContentDocumentLink
        $linkData = [
            'ContentDocumentId' => $contentDocumentId,
            'LinkedEntityId' => $leadId,
            'ShareType' => 'I', // Inferred permission
            'Visibility' => 'AllUsers'
        ];

        $linkResponse = Http::withHeaders([
            'Authorization' => "Bearer {$this->accessToken}",
            'Content-Type'  => 'application/json'
        ])->post("{$this->instanceUrl}/services/data/v64.0/sobjects/ContentDocumentLink", $linkData);

        if (!$linkResponse->successful()) {
            throw new \Exception('Failed to link file to lead: ' . $linkResponse->body());
        }

        return [
            'content_version_id' => $contentVersionId,
            'content_document_id' => $contentDocumentId,
            'link_id' => $linkResponse->json()['id'],
            'lead_id' => $leadId,
            'file_name' => $file->getClientOriginalName(),
            'title' => $title
        ];
    }
}
