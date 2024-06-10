<?php
    header("Content-Type: image/png");

    $fullname = @$_GET['fullname'];
    $user_id = @$_GET['user'];

    if ($user_id) {
        header("location: slack_avatar.php?user={$user_id}");
        die;
    }

    $googleColors = [
        'A' => [219, 68, 55], // Vermelho (#DB4437)
        'B' => [15, 157, 88], // Verde (#0F9D58)
        'C' => [66, 133, 244], // Azul (#4285F4)
        'D' => [244, 180, 0], // Amarelo (#F4B400)
        'E' => [70, 189, 198], // Azul claro (#46BDC6)
        'F' => [244, 180, 0], // Laranja (#F4B400)
        'G' => [15, 157, 88], // Azul escuro (#0F9D58)
        'H' => [219, 68, 55], // Vermelho claro (#DB4437)
        'I' => [244, 180, 0], // Amarelo claro (#F4B400)
        'J' => [124, 83, 195], // Roxo (#7C53C3)
        'K' => [15, 157, 88], // Verde claro (#0F9D58)
        'L' => [52, 168, 83], // Azul celeste (#34A853)
        'M' => [219, 68, 55], // Vermelho escuro (#DB4437)
        'N' => [244, 180, 0], // Laranja claro (#F4B400)
        'O' => [255, 109, 0], // Rosa (#FF6D00)
        'P' => [52, 168, 83], // Verde-água (#34A853)
        'Q' => [66, 133, 244], // Azul escuro (#4285F4)
        'R' => [15, 157, 88], // Verde escuro (#0F9D58)
        'S' => [124, 83, 195], // Roxo claro (#7C53C3)
        'T' => [52, 168, 83], // Verde limão (#34A853)
        'U' => [255, 109, 0], // Rosa claro (#FF6D00)
        'V' => [70, 189, 198], // Azul-piscina (#46BDC6)
        'W' => [244, 180, 0], // Amarelo suave (#F4B400)
        'X' => [154, 160, 166], // Cinza (#9AA0A6)
        'Y' => [124, 83, 195], // Roxo escuro (#7C53C3)
        'Z' => [66, 133, 244], // Azul-petróleo (#4285F4)
    ];

    $firstLetter = strtoupper(substr($fullname, 0, 1));
    $bg = $googleColors[$firstLetter];

    $im = @imagecreate(40, 40) or die("Cannot Initialize new GD image stream");

    $background_color = imagecolorallocate($im, $bg[0], $bg[1], $bg[2]);
    $text_color = imagecolorallocate($im, 251, 252, 249);

                              
    if (strpos($fullname, $scape=' ')) 
    {
        $fullname = explode($scape, $fullname, 2);
    } 
    else if (strpos($fullname, $scape='_')) 
    {
        $fullname = explode($scape, $fullname, 2);
    } 
    else if (strpos($fullname, $scape='.')) 
    {
        $fullname = explode($scape, $fullname, 2);
    } 
    else 
    {
        $fullname_dot = [$fullname];
    }

    $string = '';

    if (count($fullname)) {
        $string = '';
        if ($first = @$fullname[0][0]) $string .= $first;
        if ($second = @$fullname[1][0]) $string .= $second;

        $string = strtoupper($string);
    } else {
        $string = "OP";
    }

    imagestring($im, $font=18, $x=12, $y=12, $string, $text_color);

    imagepng($im);
    imagedestroy($im);
?>