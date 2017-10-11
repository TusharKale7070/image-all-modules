<?php

if (!empty($_FILES['image_name']['name'])) {

    $path = $_FILES['image_name']['name'];
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    $config['allowed_types'] = 'gif|jpg|png|bmp|jpeg';
    $config['upload_path'] = 'assets/uploads/event/';
    $milliseconds = round(microtime(true)) . '.' . $ext;
    $config['file_name'] = $milliseconds;

    $this->load->library('upload');
    $this->upload->initialize($config);

    if ($this->upload->do_upload('image_name')) {
        $image_name = $milliseconds;
        $this->load->library('image_lib');
        $image_data = $this->upload->data();

        // Absolute path
        $abs_path = str_replace('system/', '', BASEPATH);
        $abs_path = preg_replace("#([^/])/*$#", "\\1/", $abs_path);

        $src = $abs_path . 'assets/uploads/event/' . $image_name;
        $dest = $abs_path . 'assets/uploads/event/thumbs/' . $image_name;

        $desired_width = "150";
        $source_image = $this->imageCreateFromAny($src, $ext);
        $width = imagesx($source_image);
        $height = imagesy($source_image);

        // find the "desired height" of this thumbnail, relative to the desired width  /
        $desired_height = floor($height * ($desired_width / $width));

        //create a new, "virtual" image /
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        //copy source image at a resized size /
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

        //create the physical thumbnail image to its destination /

        imagejpeg($virtual_image, $dest);

        // Absolute path
        
        //  Image magic library for crop image
        //  $this->load->library('Image_magician');
        //  $source_image =  $abs_path.'assets/uploads/event/'.$image_name;
        //  $new_image =  $abs_path.'assets/uploads/event/thumbs/'.$image_name;
        //  $this->image_magician->crop_image($source_image, 115, 115, 0, 0, 'Center', FALSE, $new_image);
        //  $config = array(
        //  'source_image' => $image_data['full_path'],
        //      'new_image' => 'assets/uploads/event/thumbs/',
        //      'maintain_ration' => false,
        //      'width' => '90',
        //      'height' => '90'
        //   );
        //   $this->image_lib->initialize($config);
        //   $this->image_lib->resize();
    }
}

function imageCreateFromAny($filepath, $type) {

    if ($type == 'gif') {
        $im = imageCreateFromGif($filepath);
    } elseif ($type == 'jpg' || $type == 'jpeg') {
        $im = imageCreateFromJpeg($filepath);
    } elseif ($type == 'png') {
        $im = imageCreateFromPng($filepath);
    } elseif ($type == 'bmp') {
        $im = imageCreateFromBmp($filepath);
    }

    return $im;
}

?>