<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class ModalDOM extends Controller {
    
    public function __invoke(int $id): string {
        $path = 'dom_copy_' .$id. '.html';
        if(Storage::exists($path)) {
            return Storage::get('dom_copy_' .$id. '.html');
        } else {
            return 'file does not exist: ' .$path ;
        }
    }
}
