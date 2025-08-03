<?php

function tgl_indo($tgl) { 
    if (strlen($tgl) != 10 || substr($tgl, 4, 1) != '-' || substr($tgl, 7, 1) != '-') 
    {
        return "Format tanggal tidak valid";
    }

    $tanggal = substr($tgl, 8, 2);
    $bulan   = getBulan((int)substr($tgl, 5, 2));  
    $tahun   = substr($tgl, 0, 4);

    return $tanggal . ' ' . $bulan . ' ' . $tahun;
}

function getBulan($bln) 
{
    switch ($bln) 
    {
        case 1:
            return "Januari";
        case 2:
            return "Februari";
        case 3:
            return "Maret";
        case 4:
            return "April";
        case 5:
            return "Mei";
        case 6:
            return "Juni";
        case 7:
            return "Juli";
        case 8:
            return "Agustus";
        case 9:
            return "September";
        case 10:
            return "Oktober";
        case 11:
            return "November";
        case 12:
            return "Desember";
        default:
            return "Bulan tidak valid";  
    }
} 

?>