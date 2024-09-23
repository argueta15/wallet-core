<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\WalletCollection;
use App\Services\ChatGPTService;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\WalletResource;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\BalanceResource;
use App\Services\ExportService;

class WalletController extends Controller
{
    protected $walletRepository;

    protected $categoryRepository;

    protected $chatGPTService;

    protected $exportService;

    public function __construct(
        WalletRepositoryInterface $walletRepository,
        ChatGPTService $chatGPTService,
        CategoryRepositoryInterface $categoryRepository,
        ExportService $exportService
    )
    {
        $this->walletRepository = $walletRepository;
        $this->chatGPTService = $chatGPTService;
        $this->categoryRepository = $categoryRepository;
        $this->exportService = $exportService;
    }

    /**
     * @OA\Get(
     *     path="/api/wallet",
     *     summary="Get paginated list of user transactions",
     *     description="Retrieve paginated list of transactions for the authenticated user",
     *     operationId="getUserTransactions",
     *     tags={"Wallet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of transactions per page",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of transactions",
     *         @OA\JsonContent(ref="#/components/schemas/WalletCollection")
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('user')->get('id');
        $perPage = (int) $request->query('per_page', 10);
        $page = (int) $request->query('page', 1);

        $transactions = $this->walletRepository->getUserTransactionsPaginated($userId, $perPage, $page);

        return response()->json(new WalletCollection($transactions));
    }

    /**
     * @OA\Post(
     *     path="/api/wallet",
     *     summary="Create a new transaction",
     *     description="Store a new transaction in the user's wallet",
     *     operationId="storeTransaction",
     *     tags={"Wallet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreTransactionRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transaction created",
     *         @OA\JsonContent(ref="#/components/schemas/WalletResource")
     *     )
     * )
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $userId = $request->input('user')->get('id');
        $amount = $request->input('amount') * 100;
        $description = $request->input('description');
        $type = $request->input('type');

        $category = $this->chatGPTService->getCategoryFromDescription($description);

        $findCategory = $this->categoryRepository->findOrCreateCategoryByUser($userId, $category);

        $transaction = $this->walletRepository->create([
            'user_id' => $userId,
            'amount' => $amount,
            'description' => $description,
            'type' => $type,
            'category_id' => $findCategory->id,
            'date' => now()->toDateString(),
        ]);

        return response()->json(new WalletResource($transaction));
    }

    /**
     * @OA\Put(
     *     path="/api/wallet/{id}",
     *     summary="Update a transaction",
     *     description="Update a transaction in the user's wallet",
     *     operationId="updateTransaction",
     *     tags={"Wallet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Transaction ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateTransactionRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction updated",
     *         @OA\JsonContent(ref="#/components/schemas/WalletResource")
     *     )
     * )
     */
    public function update(UpdateTransactionRequest $request, int $id): JsonResponse
    {
        $userId = $request->input('user')->get('id');
        $amount = $request->input('amount') * 100;
        $description = $request->input('description');
        $type = $request->input('type');

        $category = $this->chatGPTService->getCategoryFromDescription($description);

        $findCategory = $this->categoryRepository->findOrCreateCategoryByUser($userId, $category);

        $transaction = $this->walletRepository->update($id, [
            'amount' => $amount,
            'description' => $description,
            'type' => $type,
            'category_id' => $findCategory->id,
        ]);

        return response()->json(new WalletResource($transaction));
    }

    /**
     * @OA\Delete(
     *     path="/api/wallet/{id}",
     *     summary="Delete a transaction",
     *     description="Delete a transaction from the user's wallet",
     *     operationId="deleteTransaction",
     *     tags={"Wallet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Transaction ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction deleted"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->walletRepository->delete($id);

        return response()->json(['message' => 'Transaction deleted successfully.']);
    }

    private function createDefaultBalance(int $userId)
    {
        return [
            'userId' => $userId,
            'totalIncome' => 0,
            'incomeThisMonth' => 0,
            'totalExpense' => 0,
            'expenseThisMonth' => 0,
            'generalBalance' => 0,
            'balanceThisMonth' => 0,
            'tip' => 'Ingresa tus datos financieros para obtener un consejo.',
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/balances",
     *     summary="Get user balance",
     *     description="Retrieve the current balance for the authenticated user",
     *     operationId="getBalance",
     *     tags={"Wallet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Current balance",
     *         @OA\JsonContent(ref="#/components/schemas/BalanceResource")
     *     )
     * )
     */
    public function getBalance(Request $request): JsonResponse
    {
        $userId = $request->input('user')->get('id');
        $response = $this->createDefaultBalance($userId);

        $balance = DB::table('user_financial_summary')
            ->where('user_id', $userId)
            ->first();

        if ($balance){
            $balance->tip = $this->chatGPTService->getTipForUser($balance);
            $response = new BalanceResource($balance);
        }

        return response()->json($response);
    }

    /**
     * @OA\Get(
     *     path="/api/download-excel",
     *     summary="Download user transactions as Excel",
     *     description="Download all user transactions as an Excel file",
     *     operationId="downloadExcel",
     *     tags={"Wallet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Excel file download"
     *     )
     * )
     */
    public function downloadExcel(Request $request): JsonResponse
    {
        $userId = $request->input('user')->get('id');

        $transactions = $this->walletRepository->getAllUserTransactions($userId);

        $headings = [
            'ID',
            'Amount',
            'Description',
            'Type',
            'Category',
            'Date',
        ];

        $transactions = $transactions->map(function ($transaction) {
            return [
                $transaction->id,
                $transaction->amount / 100,
                $transaction->description,
                $transaction->type,
                $transaction->category->name,
                $transaction->date,
            ];
        });

        $response = $this->exportService->downloadExcel($headings, $transactions);

        if (isset($response['error'])) {
            return response()->json($response, 500);
        }

        return response()->json($response);
    }
}
