<?php

namespace UniSharp\LaravelFilemanager\controllers;

use UniSharp\LaravelFilemanager\Lfm;
use UniSharp\LaravelFilemanager\LfmPath;
use UniSharp\LaravelFilemanager\LfmStorage;
use UniSharp\LaravelFilemanager\traits\LfmHelpers;

class LfmController extends Controller
{
    protected static $success_response = 'OK';

    public function __construct()
    {
        $this->applyIniOverrides();
    }

    public function __get($var_name)
    {
        if ($var_name == 'lfm') {
            return app(LfmPath::class);
        } elseif ($var_name == 'helper') {
            return app(Lfm::class);
        }
    }

    /**
     * Show the filemanager.
     *
     * @return mixed
     */
    public function show()
    {
        // dd($this->lfm->files()[1]->hasThumb());
        // dd(app()::VERSION > "5.1.0");
        return view('laravel-filemanager::index');
    }

    public function getErrors()
    {
        $arr_errors = [];

        if (! extension_loaded('gd') && ! extension_loaded('imagick')) {
            array_push($arr_errors, trans('laravel-filemanager::lfm.message-extension_not_found'));
        }

        $type_key = $this->helper->currentLfmType();
        $mine_config = 'lfm.valid_' . $type_key . '_mimetypes';
        $config_error = null;

        if (! is_array(config($mine_config))) {
            array_push($arr_errors, 'Config : ' . $mine_config . ' is not a valid array.');
        }

        return $arr_errors;
    }

    /**
     * Shorter function of getting localized error message..
     *
     * @param  mixed  $error_type  Key of message in lang file.
     * @param  mixed  $variables   Variables the message needs.
     * @return string
     */
    public function error($error_type, $variables = [])
    {
        throw new \Exception(trans(Lfm::PACKAGE_NAME . '::lfm.error-' . $error_type, $variables));
    }

    /**
     * Overrides settings in php.ini.
     *
     * @return null
     */
    public function applyIniOverrides()
    {
        if (count(config('lfm.php_ini_overrides')) == 0) {
            return;
        }

        foreach (config('lfm.php_ini_overrides') as $key => $value) {
            if ($value && $value != 'false') {
                ini_set($key, $value);
            }
        }
    }
}
