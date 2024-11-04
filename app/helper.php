<?php

if (!function_exists('processImageUpload')) {
    function processImageUpload($file, $name)
    {
        $image_name = $name . '_' . uniqid() . '_' . time() . '.' . $file->extension();
        $file->storeAs("public/books/{$image_name}");

        return $image_name;
    }
}
