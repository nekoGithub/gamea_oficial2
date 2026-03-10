<?php

namespace App\Services;

use TCPDF;

class AuditoriaPDF extends TCPDF
{
    // Colores institucionales
    private $colorPrimario = [211, 47, 47];
    private $colorCyan = [0, 188, 212];
    private $colorOscuro = [33, 33, 33];
    private $colorTexto = [66, 66, 66];
    private $colorGrisClaro = [245, 245, 245];

    // ✅ PATH DEL LOGO (no cargar en memoria)
    private $logoPath = null;
    private $logoExists = false;

    /**
     * Constructor - Verificar logo una sola vez
     */
    public function __construct($orientation = 'L', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);

        // ✅ Solo verificar si existe una vez
        $this->logoPath = public_path('img/logo1.png');
        $this->logoExists = file_exists($this->logoPath);

        // ✅ Habilitar caché de imágenes de TCPDF
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
    }

    public function Header()
    {
        // ========== BANDA ROJA SUPERIOR CON DISEÑO GEOMÉTRICO ==========
        $this->SetFillColor(211, 47, 47);
        $this->Rect(0, 0, $this->getPageWidth(), 30, 'F');

        // Triángulos decorativos en el header (efecto geométrico)
        $this->SetAlpha(0.1);
        $this->SetFillColor(255, 255, 255);

        $puntos1 = array(0, 0, 60, 0, 0, 30);
        $this->Polygon($puntos1, 'F');

        $puntos2 = array($this->getPageWidth() - 80, 0, $this->getPageWidth(), 0, $this->getPageWidth(), 30);
        $this->Polygon($puntos2, 'F');

        $this->SetAlpha(1);

        // Logo institucional
        if ($this->logoExists) {
            // ✅ TCPDF cachea automáticamente las imágenes
            $this->Image($this->logoPath, 15, 10, 15, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            $xTexto = 32;
        } else {
            // Si no hay logo, dibujar un círculo placeholder
            $this->SetFillColor(255, 255, 255);
            $this->Circle(22, 15, 7, 0, 360, 'F');
            $this->SetTextColor(211, 47, 47);
            $this->SetFont('helvetica', 'B', 12);
            $this->SetXY(18, 11);
            $this->Cell(8, 8, 'G', 0, 0, 'C');
            $xTexto = 32;
        }

        // Título GAMEA
        $this->SetFont('helvetica', 'B', 20);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY($xTexto, 10);
        $this->Cell(60, 7, 'GAMEA', 0, 0, 'L');

        // Subtítulo institucional
        $this->SetFont('helvetica', '', 7);
        $this->SetXY($xTexto, 18);
        $this->Cell(80, 4, 'Gobierno Autónomo Municipal de El Alto', 0, 0, 'L');

        // Título del reporte (derecha)
        $this->SetFont('helvetica', 'B', 14);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(150, 10);
        $this->Cell(0, 6, 'REPORTE DE AUDITORÍAS', 0, 1, 'R');

        // Fecha (derecha)
        $this->SetFont('helvetica', '', 8);
        $this->SetXY(150, 18);
        $this->Cell(0, 4, date('d/m/Y H:i:s'), 0, 0, 'R');

        // ========== LÍNEA CYAN HORIZONTAL ==========
        $this->SetLineStyle(['width' => 2, 'color' => [0, 188, 212]]);
        $this->Line(15, 32, $this->getPageWidth() - 15, 32);
    }

    public function Footer()
    {
        $this->SetY(-20);

        // Banda inferior roja
        $this->SetFillColor(211, 47, 47);
        $this->Rect(0, $this->GetY() + 10, $this->getPageWidth(), 20, 'F');

        // Línea cyan superior
        $this->SetLineStyle(['width' => 1, 'color' => [0, 188, 212]]);
        $this->Line(15, $this->GetY(), $this->getPageWidth() - 15, $this->GetY());
        $this->Ln(2);

        $this->SetFont('helvetica', '', 7);
        $this->SetTextColor(100, 100, 100);

        $this->SetX(15);
        $this->Cell(100, 5, 'Sistema GAMEA - Gestión de Sistemas', 0, 0, 'L');

        $this->SetFont('helvetica', 'B', 8);
        $this->SetTextColor(45, 45, 45);
        $this->Cell(0, 5, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'R');
    }

    /**
     * Marca de agua con logo GAMEA (optimizada)
     */
    public function setMarcaAgua()
    {
        if ($this->logoExists) {
            // ✅ Configuración optimizada
            $logoSize = 120;  // Tamaño reducido para mejor rendimiento
            $opacidad = 0.06;

            $this->SetAlpha($opacidad);

            $x = ($this->getPageWidth() - $logoSize) / 2;
            $y = ($this->getPageHeight() - $logoSize) / 2;

            // ✅ TCPDF cachea la imagen automáticamente
            // Reducir DPI para mejor rendimiento
            $this->Image($this->logoPath, $x, $y, $logoSize, '', 'PNG', '', 'M', false, 150, '', false, false, 0, false, false, false);

            $this->SetAlpha(1);
        } else {
            // Marca de agua textual (muy rápida)
            $this->SetAlpha(0.04);
            $this->StartTransform();
            $this->Rotate(45, $this->getPageWidth() / 2, $this->getPageHeight() / 2);
            $this->SetFont('helvetica', 'B', 100);
            $this->SetTextColor(230, 230, 230);
            $this->Text($this->getPageWidth() / 2 - 70, $this->getPageHeight() / 2, 'GAMEA');
            $this->StopTransform();
            $this->SetAlpha(1);
        }
    }
}
