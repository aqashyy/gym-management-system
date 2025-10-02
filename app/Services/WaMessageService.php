<?php

namespace App\Services;

use App\Models\WaMessageTemplate;
use Carbon\Carbon;

class WaMessageService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getMessage($customer_id, string $templateName, array $data): ?string
    {
        $template = WaMessageTemplate::where('customer_id', $customer_id)
                                   ->where('name', $templateName)
                                   ->where('is_active', true)
                                   ->first();

        if (! $template) {
            return null;
        }

        $message = $template->content;
        // dd($message);
        // Replace {{key}} with actual values
        foreach ($data as $key => $value) {
            $message = str_replace('{'.$key.'}', $value, $message);
        }

        return $message;
    }

    public function getWaMsgLink(int $customer_id, string $templateName, $member): string|null
    {
        // dd($customer_id, $member->Plan, $templateName);
        if($templateName == 'welcome') {

            $message = $this->getMessage($customer_id,'welcome', [
                'member_name' => $member->name,
                'gym_name'    => strtoupper($member->Customer->name),
            ]);

        } elseif ($templateName == 'plan_expired') {

            $message = $this->getMessage($customer_id,'plan_expired', [
                'member_name' => $member->name,
                'plan_name' => $member->Plan->name,
                'plan_expiry' => Carbon::parse($member->plan_expiry)->format('d M Y'),
                'gym_name'    => strtoupper($member->Customer->name),
            ]);

        } elseif ($templateName == 'plan_expire_on') {

            $message = $this->getMessage($customer_id,'plan_expire_on', [
                'member_name' => $member->name,
                'plan_name' => $member->Plan->name,
                'plan_expiry' => Carbon::parse($member->plan_expiry)->format('d M Y'),
                'gym_name'    => strtoupper($member->Customer->name),
            ]);

        } elseif ($templateName == 'birthday') {

            $message = $this->getMessage($customer_id,'birthday', [
                'member_name' => $member->name,
                'gym_name'    => strtoupper($member->Customer->name),
            ]);
        } elseif ($templateName == 'payment') {

            $message = $this->getMessage($customer_id,'payment', [
                'member_name'       => $member->name,
                'plan_name'         => $member->Plan->name,
                'plan_expiry'       => Carbon::parse($member->plan_expiry)->format('d M Y'),
                'gym_name'          => strtoupper($member->Customer->name),
                'total_amount'      => $member->total_amount,
                'recieved_amount'   => $member->recieved_amount,
                'balance_amount'    => $member->balance_amount,

            ]);

        }

        if ($message) {
            $phone = $member->phone;
            $url = "https://wa.me/{$phone}?text=" . urlencode($message);
            // return redirect($url); 
            return $url;
        }
        return null;
    }
}
