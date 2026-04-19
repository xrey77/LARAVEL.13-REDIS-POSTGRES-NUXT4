<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA; 
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Exception;
use Illuminate\Support\Facades\Cache;

class ActivatemfaController extends Controller
{

 public function enableMfa($id, Request $request) {
    if (Auth::guard('sanctum')->check()) {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found...'], 404);
        }

        $isEnabled = $request->Twofactorenabled;
        $cacheKey = "user_mfa_qr_{$id}";

        if ($isEnabled) {
            $qrcode = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($user) {
                $issuer = config('services.issuer_service.key');
                $google2fa = new Google2FA();
                $secretKey = $google2fa->generateSecretKey();
                
                $qrCodeUrl = $google2fa->getQRCodeUrl($issuer, $user->email, $secretKey);

                $renderer = new ImageRenderer(
                    new RendererStyle(200),
                    new ImagickImageBackEnd()
                );
                $writer = new Writer($renderer);
                
                $qrcode_image_string = $writer->writeString($qrCodeUrl);        
                $qrcode_base64 = base64_encode($qrcode_image_string);

                $user->secretkey = encrypt($secretKey);
                $user->save();

                return 'data:image/png;base64,' . $qrcode_base64;
            });

            return response()->json([
                'message' => 'Multi-Factor Authenticator has been Enabled, please Scan the QR code!',
                'qrcodeurl' => $qrcode
            ], 200);

        } else {
            Cache::forget($cacheKey);
            $user->qrcodeurl = null;
            $user->secretkey = null;
            $user->save();

            return response()->json(['message' => 'Multi-Factor Authenticator has been Disabled successfully.'], 200);
        }
    }

    return response()->json(['message' => 'Un-Authorized access.'], 401);
 }
}
