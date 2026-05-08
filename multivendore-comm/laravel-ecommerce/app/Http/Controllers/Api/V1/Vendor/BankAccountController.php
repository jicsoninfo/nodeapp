<?php
namespace App\Http\Controllers\Api\V1\Vendor;
use App\Http\Controllers\Controller;
use App\Models\VendorBankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class BankAccountController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->vendor->bankAccounts()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'account_holder'   => 'required|string|max:200',
            'bank_name'        => 'required|string|max:200',
            'account_number'   => 'required|string',
            'routing_number'   => 'nullable|string',
            'is_primary'       => 'boolean',
        ]);

        $vendor = $request->user()->vendor;
        $this->authorize('manageBankAccounts', $vendor);

        if ($data['is_primary'] ?? false) {
            $vendor->bankAccounts()->update(['is_primary' => false]);
        }

        $account = $vendor->bankAccounts()->create([
            'account_holder'    => $data['account_holder'],
            'bank_name'         => $data['bank_name'],
            'account_number_enc'=> Crypt::encryptString($data['account_number']),
            'routing_number_enc'=> isset($data['routing_number']) ? Crypt::encryptString($data['routing_number']) : null,
            'is_primary'        => $data['is_primary'] ?? false,
        ]);

        return response()->json(['data' => $account, 'message' => 'Bank account added.'], 201);
    }

    public function destroy(Request $request, VendorBankAccount $account): JsonResponse
    {
        abort_if($account->vendor_id !== $request->user()->vendor->id, 403);
        abort_if($account->is_primary, 422, 'Cannot delete the primary bank account. Set another as primary first.');
        $account->delete();
        return response()->json(['message' => 'Bank account removed.']);
    }

    public function setPrimary(Request $request, VendorBankAccount $account): JsonResponse
    {
        abort_if($account->vendor_id !== $request->user()->vendor->id, 403);
        $request->user()->vendor->bankAccounts()->update(['is_primary' => false]);
        $account->update(['is_primary' => true]);
        return response()->json(['message' => 'Primary account updated.']);
    }
}
