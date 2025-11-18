<?php
include '../config/koneksi.php';
require('../lib/fpdf/fpdf.php');

$tanggal_awal = $_GET['tanggal_awal'] ?? date('Y-m-d');
$tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d');

$query = "SELECT b.*, l.harga_mobil, l.harga_motor 
          FROM booking b
          JOIN layanan l ON b.layanan = l.nama_layanan
          WHERE b.status = 'Selesai' 
          AND (b.tanggal_booking BETWEEN '$tanggal_awal' AND '$tanggal_akhir')
          ORDER BY b.tanggal_booking ASC";
          
$result_laporan = mysqli_query($conn, $query);

class PDF extends FPDF
{
    
    function Header()
    {
        $this->SetFont('Arial','B',14);
        $this->Cell(0, 10, 'Laporan Pendapatan - ProClean Vehicle Wash', 0, 1, 'C');
        $this->SetFont('Arial','',10);
        
        $tanggal_awal_f = date('d M Y', strtotime($_GET['tanggal_awal'] ?? date('Y-m-d')));
        $tanggal_akhir_f = date('d M Y', strtotime($_GET['tanggal_akhir'] ?? date('Y-m-d')));
        $this->Cell(0, 7, "Periode: $tanggal_awal_f s/d $tanggal_akhir_f", 0, 1, 'C');
        $this->Ln(5); 
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo().'/{nb}',0,0,'C');
    }

    function FancyTable($header, $data)
    {

        $this->SetFillColor(29, 41, 59); 
        $this->SetTextColor(255);
        $this->SetDrawColor(222, 226, 230); 
        $this->SetFont('','B', 9);
        
        $w = array(10, 45, 30, 30, 45, 30);
        
        for($i=0; $i<count($header); $i++)
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        
        $this->SetFillColor(248, 249, 250);
        $this->SetTextColor(0);
        $this->SetFont('','', 9);
        
        $fill = false;
        $total_pendapatan = 0;
        
        foreach($data as $row)
        {
            $harga = ($row['jenis_kendaraan'] == 'Mobil') ? $row['harga_mobil'] : $row['harga_motor'];
            $total_pendapatan += $harga;
            
            $this->Cell($w[0], 6, $this->PageNo() == 1 ? $this->rowNum++ : $this->rowNum++, 'LR', 0, 'C', $fill); 
            $this->Cell($w[1], 6, $row['nama'], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, date('d M Y', strtotime($row['tanggal_booking'])), 'LR', 0, 'C', $fill);
            $this->Cell($w[3], 6, $row['jenis_kendaraan'], 'LR', 0, 'C', $fill);
            $this->Cell($w[4], 6, $row['layanan'], 'LR', 0, 'L', $fill);
            $this->Cell($w[5], 6, "Rp " . number_format($harga), 'LR', 0, 'R', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln();
        
        $this->SetFont('','B', 10);
        $this->Cell($w[0] + $w[1] + $w[2] + $w[3] + $w[4], 8, 'Total Pendapatan', 1, 0, 'R');
        $this->SetFillColor(233, 236, 239); 
        $this->Cell($w[5], 8, "Rp " . number_format($total_pendapatan), 1, 1, 'R', true);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages(); 
$pdf->AddPage();
$pdf->rowNum = 1; 

$header = array('No', 'Nama Pelanggan', 'Tanggal', 'Kendaraan', 'Layanan', 'Harga');

$data_laporan = [];
if ($result_laporan) {
    while ($row = mysqli_fetch_assoc($result_laporan)) {
        $data_laporan[] = $row;
    }
}

$pdf->FancyTable($header, $data_laporan);

$pdf->Output('I', "Laporan_Pendapatan_ProClean.pdf");
?>