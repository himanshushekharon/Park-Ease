<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KycController extends Controller
{
    public function showKycForm()
    {
        return view('owner.kyc');
    }

    public function submitKyc(Request $request)
    {
        $user = Auth::user();
        $isVerified = $user->kyc_status === 'verified';

        $validated = $request->validate([
            'phone' => 'required|string|max:15',
            'aadhaar_no' => 'required|string|size:12',
            'photo' => ($isVerified ? 'nullable' : 'required') . '|image|min:10|max:2048',
            'property_image' => ($isVerified ? 'nullable' : 'required') . '|image|min:10|max:2048',
        ]);

        $user->phone = $validated['phone'];
        $user->aadhaar_no = $validated['aadhaar_no'];
        
        if ($request->hasFile('photo')) {
            $photoFilename = $user->id . '_photo.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->move(public_path('kyc/photos'), $photoFilename);
            $user->photo_path = 'kyc/photos/' . $photoFilename;
        }
        
        if ($request->hasFile('property_image')) {
            $propertyFilename = $user->id . '_property.' . $request->file('property_image')->getClientOriginalExtension();
            $request->file('property_image')->move(public_path('kyc/properties'), $propertyFilename);
            $user->property_image_path = 'kyc/properties/' . $propertyFilename;
        }
        
        // Auto verify for demo purposes
        $user->kyc_status = 'verified';
        $user->onboarding_completed = true;
        
        $user->save();

        return redirect('/owner/dashboard');
    }
}
