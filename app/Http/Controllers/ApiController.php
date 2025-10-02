<?php

namespace App\Http\Controllers;

use App\Models\FingerDevice;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function __construct(
        private MemberService $memberService
    ) {
        
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        // dd($credentials);
        if( !Auth::attempt($credentials) ) {

            return response()->json([
                'status'    =>  false,
                'message'   =>  "Invalid credentials!"
            ]);

        }

        $user = Auth::user();
        $fingerDevice = FingerDevice::where('customer_id', $user->Customer->id)
                            ->first();

        return response()->json([
            'status'    =>  true,
            'message'   =>  "Login success",
            'token'     =>  $user->createToken('middleware-app')->plainTextToken,
            'ip'        =>  $fingerDevice->ip ?? '0',
            'port'      =>  $fingerDevice->port ?? '0'
        ]);
    }
    
    public function checkExpiry(Request $request) 
    {
        $request->validate([
            'fingerid' => 'required|int'
        ]);

        $customerId = $request->user()->Customer->id;

        return response()->json([
            'status'    =>  $this->memberService->planCheckCustomerByFingerId($customerId, $request->fingerid)
        ],200);

        // dd($customerId, $fingerId);
    }
}
