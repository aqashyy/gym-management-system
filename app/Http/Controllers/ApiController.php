<?php

namespace App\Http\Controllers;

use App\Services\MemberService;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function __construct(
        private MemberService $memberService
    ) {
        
    }
    
    public function checkExpiry($customerId, $fingerId) 
    {

        return response()->json([
            'status'    =>  $this->memberService->planCheckCustomerByFingerId($customerId, $fingerId)
        ],200);

        // dd($customerId, $fingerId);
    }
}
