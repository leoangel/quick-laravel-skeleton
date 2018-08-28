<?php


namespace App\Functions;


use App\Constants\BusinessConstants;
use BarcodeBakery\Barcode\BCGcode39extended;
use BarcodeBakery\Common\BCGColor;
use BarcodeBakery\Common\BCGDrawing;
use BarcodeBakery\Common\BCGFontFile;

class UtilFunction
{
    public static function checkMobileFormat($mobile)
    {
        if (empty($mobile)) {
            return false;
        }

        if (mb_strlen($mobile) !== 11) {
            return false;
        }

        if (!preg_match('/^1[0-9]{10}$/', $mobile)) {
            return false;
        }

        return true;
    }

    public static function getPageAndOffset($page, $size = BusinessConstants::PAGE_SIZE)
    {
        if (empty($page) || $page < 0) {
            $page = 1;
        }

        if ($page > BusinessConstants::MAX_PAGE) {
            $page = BusinessConstants::MAX_PAGE;
        }

        $offset = ($page - 1) * $size;

        return [$page, $offset, $size];
    }

    public static function renderPage($total, $curPage, $pageSize = BusinessConstants::PAGE_SIZE)
    {
        $totalPage = ceil($total / $pageSize);

        $nextPage = ($curPage < $totalPage && $curPage < BusinessConstants::MAX_PAGE) ? $curPage + 1 : 0;

        return [
            'totalnum' => $total,
            'nextpage' => $nextPage,
            'totalpage' => $totalPage,
            'curpage' => (int)$curPage,
            'pagesize' => intval($pageSize)
        ];
    }

    public static function getBarcode($str, $base64 = true)
    {
        $font = new BCGFontFile(resource_path('fonts/arial.ttf'), 15);
        $colorFront = new BCGColor(0, 0, 0);
        $colorBack = new BCGColor(255, 255, 255);


        $code = new BCGcode39extended();
        $code->setScale(3);
        $code->setThickness(25);
        $code->setForegroundColor($colorFront);
        $code->setBackgroundColor($colorBack);
        $code->setFont($font);
        $code->parse($str);

        $drawing = new BCGDrawing('', $colorBack);
        $drawing->setBarcode($code);
        $drawing->draw();
        ob_start();
        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
        $ret = ob_get_contents();
        ob_end_clean();

        return $base64 ? base64_encode($ret) : $ret;
    }
}