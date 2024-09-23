<?php

namespace App\Services;

use OpenAI;
use Illuminate\Support\Facades\Log;

class ChatGPTService
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
    }

    /**
     * Obtener categoría de una descripción.
     *
     * @param string $description
     * @return string
     */
    public function getCategoryFromDescription(string $description): string
    {
        try{
            $response = $this->client->chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that classifies transactions into categories like food, entertainment, bills, travel, etc. Please respond with a JSON object in the following format: {"category": "the category name"}.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Classify the following transaction description into a category: $description"
                    ]
                ],
                'max_tokens' => 10,
                 'temperature' => 0.7,
            ]);
          
            $categoryJson = $response['choices'][0]['message']['content'] ?? '{}';
            $categoryData = json_decode($categoryJson, true);
          
            $category = $categoryData['category'] ?? 'Unknown';
          
            return trim($category);
        } catch (\Exception $e) {
            Log::error('Error getting category from description: ' . $e->getMessage());
            return 'Unknown';
        }
    }

    public function getTipForUser($balance): string
    {
        try{
            if (!$balance) {
                return 'Ingresa tus datos financieros para obtener un consejo.';
            }
            $financialData = [
                'totalIncome' => $balance->total_income / 100,
                'incomeThisMonth' => $balance->income_this_month / 100,
                'totalExpense' => $balance->total_expense / 100,
                'expenseThisMonth' => $balance->expense_this_month / 100,
                'generalBalance' => $balance->general_balance / 100,
                'balanceThisMonth' => $balance->balance_this_month / 100,
            ];
        
            $description = "Total income: {$financialData['totalIncome']} MXN. Income this month: {$financialData['incomeThisMonth']} MXN. Total expense: {$financialData['totalExpense']} MXN. Expense this month: {$financialData['expenseThisMonth']} MXN. General balance: {$financialData['generalBalance']} MXN. Balance this month: {$financialData['balanceThisMonth']} MXN.";
    
            $response = $this->client->chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that provides actionable and relevant financial tips to users. Based on the provided financial data, respond with a concise tip in Spanish that is no longer than 300 characters.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Provide a tip for the user based on the following financial data: $description"
                    ]
                ],
                'max_tokens' => 150,
                'temperature' => 0.7,
            ]);
    
            $tipJson = $response['choices'][0]['message']['content'] ?? 'Tip not available';
            return trim($tipJson);
        } catch (\Exception $e) {
            Log::error('Error getting tip for user: ' . $e->getMessage());
            return 'Tip not available';
        }
    }
}
