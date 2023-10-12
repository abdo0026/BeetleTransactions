<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFileExtentions
{
    private $availableExt = ['jpeg','jpg','png','gif','pdf','csv','doc','docx','txt','ppt','pptx','mp3','mp4','xls','xlsx','svg','mov','webm'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $output = new \stdClass();

        $files = $request->files;

        $isRightExt = $this->checkExt($files);

        if(!$isRightExt) {
            $output->Error = ['wrong extension file'];
            return response()->json($output, 422);
        }

        return $next($request);
    }


    private  function checkExt($files)
    {
        foreach ($files as $key => $value) {

            if(!is_array($value) && $value->isFile()) {
                $ext = $value->getClientOriginalExtension();
                if (!in_array(strtolower($ext), $this->availableExt))
                    return false;
            }
            else {

                if(is_array($value) || is_object($value)) {
                    $isRightExt = $this->checkExt($value);
                    if (!$isRightExt)
                        return false;
                }
            }

        }

        return true;
    }

}
