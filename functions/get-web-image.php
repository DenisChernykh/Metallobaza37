<?php

add_filter('wp_update_attachment_metadata', function ($metadata, $attachment_id) {
    $file = get_attached_file($attachment_id);
    $mime = get_post_mime_type($attachment_id);


    if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
        return $metadata;
    }


    if (!wp_image_editor_supports(['mime_type' => 'image/webp'])) {
        return $metadata;
    }

    $upload_dir = wp_get_upload_dir();


    $make_webp = function ($abs, $rel, $size_key = null) use (&$metadata) {
        $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $abs);
        if (file_exists($webp_path)) {
            $image_size = wp_getimagesize($webp_path);
            if ($size_key) {
                $metadata['sizes'][$size_key . '-webp'] = [
                    'file' => basename($webp_path),
                    'width' => $image_size[0] ?? 0,
                    'height' => $image_size[1] ?? 0,
                    'mime-type' => 'image/webp',
                ];
            }
            return;
        }

        $editor = wp_get_image_editor($abs);
        if (!is_wp_error($editor)) {
            $editor->set_quality(85);
            $saved = $editor->save($webp_path, 'image/webp');
            if (!is_wp_error($saved) && $size_key) {
                $metadata['sizes'][$size_key . '-webp'] = [
                    'file' => basename($webp_path),
                    'width' => $saved['width'],
                    'height' => $saved['height'],
                    'mime-type' => 'image/webp',
                ];
            }
        }
    };


    $make_webp($file, $metadata['file'], 'full');


    if (!empty($metadata['sizes'])) {
        foreach ($metadata['sizes'] as $size => $data) {
            $abs = get - web - image . phptrailingslashit($upload_dir['basedir']) . dirname($metadata['file']) . '/' . $data['file'];
            $make_webp($abs, $data['file'], $size);
        }
    }

    return $metadata;
}, 10, 2);
