<?php
/*******************************************************************************
* tFPDF (based on FPDF 1.8.1)                                                 *
*                                                                              *
* Version:  1.32                                                               *
* Date:     2020-08-29                                                         *
* Author:   Ian Back <ianb@bpm1.com>                                           *
* License:  LGPL                                                               *
*******************************************************************************/

require_once(__DIR__ . '/vendor/setasign/fpdf/fpdf.php');

define('tFPDF_VERSION', '1.32');

class tFPDF extends FPDF
{
    protected $unifontSubset;
    protected $unicode;

    function __construct($orientation='P', $unit='mm', $size='A4')
    {
        parent::__construct($orientation, $unit, $size);
        $this->unifontSubset = array();
        $this->unicode = new Unicode();
    }

    /*******************************************************************************
    *                                                                              *
    *                               Public methods                                 *
    *                                                                              *
    *******************************************************************************/
    function AddFont($family, $style='', $file='', $uni=false)
    {
        if($uni) {
            if($file=='') {
                $file = str_replace(' ','',$family).strtolower($style).'.ttf';
            }
            $this->FontFiles[$file] = array('type'=>'TTF', 'originalsize'=>0);
            $ttffilename = $this->_getfontpath().'unifont/'.$file;
            $unifilename = $this->_getfontpath().'unifont/'.strtolower(substr($file,0,(strpos($file,'.'))));
            $name = '';
            $originalsize = 0;
            $ttfstat = stat($ttffilename);
            if(file_exists($unifilename.'.mtx.php')) {
                include($unifilename.'.mtx.php');
            }
            if(!isset($type) || !isset($name) || $originalsize != $ttfstat['size']) {
                $ttffile = $ttffilename;
                require_once($this->_getfontpath().'unifont/ttfonts.php');
                $ttf = new TTFontFile();
                $ttf->getMetrics($ttffile);
                $cw = $ttf->charWidths;
                $name = preg_replace('/[ ()]/','',$ttf->fullName);
                $desc = array('Ascent'=>round($ttf->ascent),
                            'Descent'=>round($ttf->descent),
                            'CapHeight'=>round($ttf->capHeight),
                            'Flags'=>$ttf->flags,
                            'FontBBox'=>'['.round($ttf->bbox[0])." ".round($ttf->bbox[1])." ".round($ttf->bbox[2])." ".round($ttf->bbox[3]).']',
                            'ItalicAngle'=>$ttf->italicAngle,
                            'StemV'=>round($ttf->stemV),
                            'MissingWidth'=>round($ttf->defaultWidth));
                $up = round($ttf->underlinePosition);
                $ut = round($ttf->underlineThickness);
                $originalsize = $ttfstat['size']+0;
                $type = 'TTF';
                // Generate metrics .php file
                $s='<?php'."\n";
                $s.='$name=\''.$name."';\n";
                $s.='$type=\''.$type."';\n";
                $s.='$desc='.var_export($desc,true).";\n";
                $s.='$up='.$up.";\n";
                $s.='$ut='.$ut.";\n";
                $s.='$ttffile=\''.$ttffile."';\n";
                $s.='$originalsize='.$originalsize.";\n";
                $s.='$fontkey=\''.$family.$style."';\n";
                $s.="?>";
                file_put_contents($unifilename.'.mtx.php',$s);
                $s = '<?php'."\n";
                $s .= '$cw='.var_export($cw,true).";\n";
                $s .= "?>";
                file_put_contents($unifilename.'.cw.dat',$s);
                $ttf->close();
                unset($ttf);
            } else {
                $cw = @include($unifilename.'.cw.dat');
            }
            $i = count($this->fonts)+1;
            if(!empty($this->AliasNbPages))
                $sbarr = range(0,57);
            else
                $sbarr = range(0,32);
            $this->fonts[$fontkey] = array('i'=>$i, 'type'=>$type, 'name'=>$name, 'desc'=>$desc, 'up'=>$up, 'ut'=>$ut, 'cw'=>$cw, 'ttffile'=>$ttffile, 'fontkey'=>$fontkey, 'subset'=>$sbarr, 'unifilename'=>$unifilename);
            $this->FontFiles[$fontkey] = array('length1'=>$originalsize, 'type'=>"TTF", 'ttffile'=>$ttffile);
            $this->FontFiles[$file] = array('type'=>"TTF");
            unset($cw);
        }
        else
            parent::AddFont($family, $style, $file);
    }

    function SetFont($family, $style='', $size=0)
    {
        if($family=='')
            $family = $this->FontFamily;
        else
            $family = strtolower($family);
        $style = strtoupper($style);
        if(strpos($style,'U')!==false) {
            $this->underline = true;
            $style = str_replace('U','',$style);
        }
        else
            $this->underline = false;
        if($style=='IB')
            $style = 'BI';
        if($size==0)
            $size = $this->FontSizePt;
        // Test if font is already selected
        if($this->FontFamily==$family && $this->FontStyle==$style && $this->FontSizePt==$size)
            return;
        // Test if used for the first time
        $fontkey = $family.$style;
        if(!isset($this->fonts[$fontkey])) {
            // Check if one of the standard fonts
            if(isset($this->CoreFonts[$fontkey])) {
                if(!isset($this->FontFiles[$fontkey])) {
                    // Load metric file
                    $file = $family;
                    if($family=='times' || $family=='helvetica')
                        $file .= strtolower($style);
                    $file .= '.php';
                    include($this->_getfontpath().$file);
                    if(!isset($name))
                        $this->Error('Could not include font metric file');
                }
                $i = count($this->fonts)+1;
                $name = $this->CoreFonts[$fontkey];
                $cw = $fpdf_charwidths[$fontkey];
                $this->fonts[$fontkey] = array('i'=>$i, 'type'=>'core', 'name'=>$name, 'up'=>-100, 'ut'=>50, 'cw'=>$cw);
            }
            else
                $this->Error('Undefined font: '.$family.' '.$style);
        }
        // Select it
        $this->FontFamily = $family;
        $this->FontStyle = $style;
        $this->FontSizePt = $size;
        $this->FontSize = $size/$this->k;
        $this->CurrentFont = &$this->fonts[$fontkey];
        if($this->fonts[$fontkey]['type']=='TTF') {
            $this->unifontSubset = $this->fonts[$fontkey]['subset'];
        }
        if($this->page>0)
            $this->_out(sprintf('BT /F%d %.2F Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
    }

    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        if(!empty($txt)) {
            if(isset($this->CurrentFont['type']) && $this->CurrentFont['type']=='TTF') {
                $txt2 = $this->_escapetext($txt);
                $txt = $this->UTF8ToUTF16BE($txt, false);
                $txt = $this->_escape($txt);
            }
        }
        parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }

    function Write($h, $txt, $link='')
    {
        if(isset($this->CurrentFont['type']) && $this->CurrentFont['type']=='TTF') {
            $txt = $this->UTF8ToUTF16BE($txt, false);
            $txt = $this->_escape($txt);
        }
        parent::Write($h, $txt, $link);
    }

    function Text($x, $y, $txt)
    {
        if(isset($this->CurrentFont['type']) && $this->CurrentFont['type']=='TTF') {
            $txt = $this->UTF8ToUTF16BE($txt, false);
            $txt = $this->_escape($txt);
        }
        parent::Text($x, $y, $txt);
    }

    function UTF8ToUTF16BE($str, $setbom=true)
    {
        if(!$this->unicode)
            return $str;
        $out = $setbom ? "\xFE\xFF" : '';
        if(function_exists('mb_convert_encoding'))
            return $out.mb_convert_encoding($str, 'UTF-16BE', 'UTF-8');
        $uni = $this->unicode->utf8_to_utf16be($str, $setbom);
        if($uni)
            return $uni;
        return $out.$str;
    }

    function _puttruetypeunicode($font)
    {
        // First write the font dictionary
        $this->_newobj();
        $this->_out('<</Type /Font');
        $this->_out('/BaseFont /'.$font['name']);
        $this->_out('/Subtype /Type0');
        $this->_out('/Encoding /Identity-H');
        $this->_out('/DescendantFonts ['.($this->n+1).' 0 R]');
        $this->_out('/ToUnicode '.($this->n+2).' 0 R');
        $this->_out('>>');
        $this->_out('endobj');

        // CIDFont
        $this->_newobj();
        $this->_out('<</Type /Font');
        $this->_out('/Subtype /CIDFontType2');
        $this->_out('/BaseFont /'.$font['name']);
        $this->_out('/CIDSystemInfo <</Registry (Adobe) /Ordering (UCS) /Supplement 0>>');
        $this->_out('/FontDescriptor '.($this->n+2).' 0 R');
        $c = 0;
        foreach($font['subset'] as $v) {
            if($v > 0) $c++;
        }
        if($c == 0) $this->_out('/W [1 ['.$font['desc']['MissingWidth'].'] ]');
        else {
            $w = '/W [';
            $ranges = array();
            $currange = 0;
            for($i = 0; $i <= 65535; $i++) {
                if(array_key_exists($i, $font['subset'])) {
                    if($currange == 0) $currange = $i;
                } elseif($currange > 0) {
                    $ranges[] = $currange.' '.($i - 1);
                    $currange = 0;
                }
            }
            if($currange > 0) {
                $ranges[] = $currange.' 65535';
            }
            $w .= implode(' ', $ranges);
            $w .= ' ]';
            $this->_out($w);
        }
        $this->_out('>>');
        $this->_out('endobj');

        // Font descriptor
        $this->_newobj();
        $this->_out('<</Type /FontDescriptor');
        $this->_out('/FontName /'.$font['name']);
        foreach($font['desc'] as $k=>$v) {
            if($k != 'type') {
                $this->_out('/'.$k.' '.$v);
            }
        }
        $this->_out('/FontFile2 '.($this->n+2).' 0 R');
        $this->_out('>>');
        $this->_out('endobj');

        // ToUnicode CMap
        $this->_newobj();
        $toUni = "/CIDInit /ProcSet findresource begin\n";
        $toUni .= "12 dict begin\n";
        $toUni .= "begincmap\n";
        $toUni .= "/CIDSystemInfo\n";
        $toUni .= "<</Registry (Adobe)\n";
        $toUni .= "/Ordering (UCS)\n";
        $toUni .= "/Supplement 0\n";
        $toUni .= ">> def\n";
        $toUni .= "/CMapName /Adobe-Identity-UCS def\n";
        $toUni .= "/CMapType 2 def\n";
        $toUni .= "1 begincodespacerange\n";
        $toUni .= "<0000> <FFFF>\n";
        $toUni .= "endcodespacerange\n";
        $toUni .= "1 beginbfrange\n";
        $toUni .= "<0000> <FFFF> <0000>\n";
        $toUni .= "endbfrange\n";
        $toUni .= "endcmap\n";
        $toUni .= "CMapName currentdict /CMap defineresource pop\n";
        $toUni .= "end\n";
        $toUni .= "end";
        $this->_out('<</Length '.(strlen($toUni)).'>>');
        $this->_putstream($toUni);
        $this->_out('endobj');

        // Font file
        $this->_newobj();
        $this->_out('<</Length1 '.$font['originalsize']);
        $this->_out('/Filter /FlateDecode');
        $this->_out('/Length '.strlen($font['file']).'>>');
        $this->_putstream($font['file']);
        $this->_out('endobj');
    }

    function _putfonts()
    {
        $nf=$this->n;
        foreach($this->diffs as $diff)
        {
            // Encodings
            $this->_newobj();
            $this->_out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.']>>');
            $this->_out('endobj');
        }
        foreach($this->FontFiles as $file=>$info)
        {
            if (!isset($info['type']) || $info['type']!='TTF') {
                // Font file embedding
                $this->_newobj();
                $this->FontFiles[$file]['n']=$this->n;
                $font='';
                $f=fopen($this->_getfontpath().$file,'rb',1);
                if(!$f)
                    $this->Error('Font file not found');
                while(!feof($f))
                    $font.=fread($f,8192);
                fclose($f);
                $compressed=(substr($file,-2)=='.z');
                if(!$compressed && isset($info['length2']))
                {
                    $header=(ord($font[0])==128);
                    if($header)
                    {
                        // Strip first binary header
                        $font=substr($font,6);
                    }
                    if($header && ord($font[$info['length1']])==128)
                    {
                        // Strip second binary header
                        $font=substr($font,0,$info['length1']).substr($font,$info['length1']+6);
                    }
                }
                $this->_out('<</Length '.strlen($font));
                if($compressed)
                    $this->_out('/Filter /FlateDecode');
                $this->_out('/Length1 '.$info['length1']);
                if(isset($info['length2']))
                    $this->_out('/Length2 '.$info['length2'].' /Length3 0');
                $this->_out('>>');
                $this->_putstream($font);
                $this->_out('endobj');
            }
        }
        foreach($this->fonts as $k=>$font)
        {
            // Font objects
            //$this->fonts[$k]['n']=$this->n+1;
            $type = $font['type'];
            $name = $font['name'];
            if($type=='Core')
            {
                // Standard font
                $this->fonts[$k]['n']=$this->n+1;
                $this->_newobj();
                $this->_out('<</Type /Font');
                $this->_out('/BaseFont /'.$name);
                $this->_out('/Subtype /Type1');
                if($name!='Symbol' && $name!='ZapfDingbats')
                    $this->_out('/Encoding /WinAnsiEncoding');
                $this->_out('>>');
                $this->_out('endobj');
            }
            elseif($type=='Type1' || $type=='TrueType')
            {
                // Additional Type1 or TrueType font
                $this->fonts[$k]['n']=$this->n+1;
                $this->_newobj();
                $this->_out('<</Type /Font');
                $this->_out('/BaseFont /'.$name);
                $this->_out('/Subtype /'.$type);
                $this->_out('/FirstChar 32 /LastChar 255');
                $this->_out('/Widths '.($this->n+1).' 0 R');
                $this->_out('/FontDescriptor '.($this->n+2).' 0 R');
                if($font['enc'])
                {
                    if(isset($font['diff']))
                        $this->_out('/Encoding '.($nf+$font['diff']).' 0 R');
                    else
                        $this->_out('/Encoding /WinAnsiEncoding');
                }
                $this->_out('>>');
                $this->_out('endobj');
                // Widths
                $this->_newobj();
                $cw=&$font['cw'];
                $s='[';
                for($i=32;$i<=255;$i++)
                    $s.=$cw[chr($i)].' ';
                $this->_out($s.']');
                $this->_out('endobj');
                // Descriptor
                $this->_newobj();
                $s='<</Type /FontDescriptor /FontName /'.$name;
                foreach($font['desc'] as $k=>$v)
                    $s.=' /'.$k.' '.$v;
                $file=$font['file'];
                if($file)
                    $s.=' /FontFile'.($type=='Type1' ? '' : '2').' '.$this->FontFiles[$file]['n'].' 0 R';
                $this->_out($s.'>>');
                $this->_out('endobj');
            }
            // TrueType embedded SUBSETS or FULL
            else if ($type=='TTF') {
                $this->fonts[$k]['n']=$this->n+1;
                require_once($this->_getfontpath().'unifont/ttfonts.php');
                $ttf = new TTFontFile();
                $fontname = 'MPDFAA'.'+'.$font['name'];
                $subset = $font['subset'];
                unset($subset[0]);
                $ttfontstream = $ttf->makeSubset($font['ttffile'], $subset);
                $ttfontsize = strlen($ttfontstream);
                $fontstream = gzcompress($ttfontstream);
                $codeToGlyph = $ttf->codeToGlyph;
                unset($codeToGlyph[0]);

                // Type0 Font
                // A composite font - a font composed of other fonts, organized hierarchically
                $this->_newobj();
                $this->_out('<</Type /Font');
                $this->_out('/Subtype /Type0');
                $this->_out('/BaseFont /'.$fontname.'');
                $this->_out('/Encoding /Identity-H');
                $this->_out('/DescendantFonts ['.($this->n + 1).' 0 R]');
                $this->_out('/ToUnicode '.($this->n + 2).' 0 R');
                $this->_out('>>');
                $this->_out('endobj');

                // CIDFontType2
                // A CIDFont whose glyph descriptions are based on TrueType font technology
                $this->_newobj();
                $this->_out('<</Type /Font');
                $this->_out('/Subtype /CIDFontType2');
                $this->_out('/BaseFont /'.$fontname.'');
                $this->_out('/CIDSystemInfo '.($this->n + 2).' 0 R');
                $this->_out('/FontDescriptor '.($this->n + 3).' 0 R');
                if (isset($font['desc']['MissingWidth'])){
                    $this->_out('/DW '.$font['desc']['MissingWidth'].'');
                }

                $this->_putTTfontwidths($font, $ttf->maxUni);

                $this->_out('/CIDToGIDMap '.($this->n + 4).' 0 R');
                $this->_out('>>');
                $this->_out('endobj');

                // ToUnicode
                $this->_newobj();
                $toUni = "/CIDInit /ProcSet findresource begin\n";
                $toUni .= "12 dict begin\n";
                $toUni .= "begincmap\n";
                $toUni .= "/CIDSystemInfo\n";
                $toUni .= "<</Registry (Adobe)\n";
                $toUni .= "/Ordering (UCS)\n";
                $toUni .= "/Supplement 0\n";
                $toUni .= ">> def\n";
                $toUni .= "/CMapName /Adobe-Identity-UCS def\n";
                $toUni .= "/CMapType 2 def\n";
                $toUni .= "1 begincodespacerange\n";
                $toUni .= "<0000> <FFFF>\n";
                $toUni .= "endcodespacerange\n";
                $toUni .= "1 beginbfrange\n";
                $toUni .= "<0000> <FFFF> <0000>\n";
                $toUni .= "endbfrange\n";
                $toUni .= "endcmap\n";
                $toUni .= "CMapName currentdict /CMap defineresource pop\n";
                $toUni .= "end\n";
                $toUni .= "end";
                $this->_out('<</Length '.(strlen($toUni)).'>>');
                $this->_putstream($toUni);
                $this->_out('endobj');

                // CIDSystemInfo dictionary
                $this->_newobj();
                $this->_out('<</Registry (Adobe)');
                $this->_out('/Ordering (UCS)');
                $this->_out('/Supplement 0');
                $this->_out('>>');
                $this->_out('endobj');

                // Font descriptor
                $this->_newobj();
                $this->_out('<</Type /FontDescriptor');
                $this->_out('/FontName /'.$fontname);
                foreach($font['desc'] as $kd=>$v) {
                    if ($kd == 'Flags') { $v = $v | 4; $v = $v & ~32; } // SYMBOLIC font flag
                    $this->_out(' /'.$kd.' '.$v);
                }
                $this->_out('/FontFile2 '.($this->n + 2).' 0 R');
                $this->_out('>>');
                $this->_out('endobj');

                // Embed CIDToGIDMap
                // A specification of the mapping from CIDs to glyph indices
                $cidtogidmap = '';
                $cidtogidmap = str_pad('', 256*256*2, "\x00");
                foreach($codeToGlyph as $cc=>$glyph) {
                    $cidtogidmap[$cc*2] = chr($glyph >> 8);
                    $cidtogidmap[$cc*2 + 1] = chr($glyph & 0xFF);
                }
                $cidtogidmap = gzcompress($cidtogidmap);
                $this->_newobj();
                $this->_out('<</Length '.strlen($cidtogidmap).'');
                $this->_out('/Filter /FlateDecode');
                $this->_out('>>');
                $this->_putstream($cidtogidmap);
                $this->_out('endobj');

                // Font file
                $this->_newobj();
                $this->_out('<</Length '.strlen($fontstream));
                $this->_out('/Filter /FlateDecode');
                $this->_out('/Length1 '.$ttfontsize);
                $this->_out('>>');
                $this->_putstream($fontstream);
                $this->_out('endobj');
                unset($ttf);
            }
            else
            {
                // Allow for additional types
                $this->fonts[$k]['n'] = $this->n+1;
                $mtd='_put'.strtolower($type);
                if(!method_exists($this,$mtd))
                    $this->Error('Unsupported font type: '.$type);
                $this->$mtd($font);
            }
        }
    }

    function _putTTfontwidths(&$font, $maxUni) {
        if (file_exists($font['unifilename'].'.cw127.php')) {
            include($font['unifilename'].'.cw127.php');
            $startcid = 128;
        } else {
            $rangeid = 0;
            $range = array();
            $prevcid = -2;
            $prevwidth = -1;
            $interval = false;
            $startcid = 1;
        }
        $cwlen = $maxUni + 1;

        // for each character
        for ($cid=$startcid; $cid<$cwlen; $cid++) {
            if ($cid==128 && (!file_exists($font['unifilename'].'.cw127.php'))) {
                if (is_writable(dirname($this->_getfontpath().'unifont/x'))) {
                    $fh = fopen($font['unifilename'].'.cw127.php',"wb");
                    $cw127='<?php'."\n";
                    $cw127.='$rangeid='.$rangeid.";\n";
                    $cw127.='$prevcid='.$prevcid.";\n";
                    $cw127.='$prevwidth='.$prevwidth.";\n";
                    if ($interval) { $cw127.='$interval=true'.";\n"; }
                    else { $cw127.='$interval=false'.";\n"; }
                    $cw127.='$range='.var_export($range,true).";\n";
                    $cw127.="?>";
                    fwrite($fh,$cw127,strlen($cw127));
                    fclose($fh);
                }
            }
            if ((!isset($font['cw'][$cid*2]) || !isset($font['cw'][$cid*2+1])) ||
                     ($font['cw'][$cid*2] == "\00" && $font['cw'][$cid*2+1] == "\00")) { continue; }

            $width = (ord($font['cw'][$cid*2]) << 8) + ord($font['cw'][$cid*2+1]);
            if ($width == 65535) { $width = 0; }
            if ($cid > 255 && (!isset($font['subset'][$cid]) || !$font['subset'][$cid])) { continue; }
            if (!isset($font['dw']) || (isset($font['dw']) && $width != $font['dw'])) {
                if ($cid == ($prevcid + 1)) {
                    if ($width == $prevwidth) {
                        if ($width == $range[$rangeid][0]) {
                            $range[$rangeid][] = $width;
                        } else {
                            array_pop($range[$rangeid]);
                            // new range
                            $rangeid = $prevcid;
                            $range[$rangeid] = array();
                            $range[$rangeid][] = $prevwidth;
                            $range[$rangeid][] = $width;
                        }
                        $interval = true;
                        $range[$rangeid]['interval'] = true;
                    } else {
                        if ($interval) {
                            // new range
                            $rangeid = $cid;
                            $range[$rangeid] = array();
                            $range[$rangeid][] = $width;
                        } else {
                            $range[$rangeid][] = $width;
                        }
                        $interval = false;
                    }
                } else {
                    $rangeid = $cid;
                    $range[$rangeid] = array();
                    $range[$rangeid][] = $width;
                    $interval = false;
                }
                $prevcid = $cid;
                $prevwidth = $width;
            }
        }
        $prevk = -1;
        $nextk = -1;
        $prevint = false;
        foreach ($range as $k => $ws) {
            $cws = count($ws);
            if (($k == $nextk) AND (!$prevint) AND ((!isset($ws['interval'])) OR ($cws < 4))) {
                if (isset($range[$k]['interval'])) {
                    unset($range[$k]['interval']);
                }
                $range[$prevk] = array_merge($range[$prevk], $range[$k]);
                unset($range[$k]);
            } else {
                $prevk = $k;
            }
            $nextk = $k + $cws;
            if (isset($ws['interval'])) {
                if ($cws > 3) {
                    $prevint = true;
                } else {
                    $prevint = false;
                }
                unset($range[$k]['interval']);
                --$nextk;
            } else {
                $prevint = false;
            }
        }
        $w = '';
        foreach ($range as $k => $ws) {
            if (count(array_count_values($ws)) == 1) {
                $w .= ' '.$k.' '.($k + count($ws) - 1).' '.$ws[0];
            } else {
                $w .= ' '.$k.' [ '.implode(' ', $ws).' ]' . "\n";
            }
        }
        $this->_out('/W ['.$w.' ]');
    }

    function _getfontpath()
    {
        return __DIR__ . '/';
    }

    function _putimages()
    {
        foreach(array_keys($this->images) as $file)
        {
            $this->_putimage($this->images[$file]);
            unset($this->images[$file]['data']);
            unset($this->images[$file]['smask']);
        }
    }

    function _putimage(&$info)
    {
        $this->_newobj();
        $info['n'] = $this->n;
        $this->_out('<</Type /XObject');
        $this->_out('/Subtype /Image');
        $this->_out('/Width '.$info['w']);
        $this->_out('/Height '.$info['h']);
        if($info['cs']=='Indexed')
            $this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
        else
        {
            $this->_out('/ColorSpace /'.$info['cs']);
            if($info['cs']=='DeviceCMYK')
                $this->_out('/Decode [1 0 1 0 1 0 1 0]');
        }
        $this->_out('/BitsPerComponent '.$info['bpc']);
        if(isset($info['f']))
            $this->_out('/Filter /'.$info['f']);
        if(isset($info['dp']))
            $this->_out('/DecodeParms <<'.$info['dp'].'>>');
        if(isset($info['trns']) && is_array($info['trns']))
        {
            $trns = '';
            for($i=0;$i<count($info['trns']);$i++)
                $trns .= $info['trns'][$i].' '.$info['trns'][$i].' ';
            $this->_out('/Mask ['.$trns.']');
        }
        if(isset($info['smask']))
            $this->_out('/SMask '.($this->n+1).' 0 R');
        $this->_out('/Length '.strlen($info['data']).'>>');
        $this->_putstream($info['data']);
        $this->_out('endobj');
        // Soft mask
        if(isset($info['smask']))
        {
            $dp = '/Predictor 15 /Colors 1 /BitsPerComponent 8 /Columns '.$info['w'];
            $smask = array('w'=>$info['w'], 'h'=>$info['h'], 'cs'=>'DeviceGray', 'bpc'=>8, 'f'=>$info['f'], 'dp'=>$dp, 'data'=>$info['smask']);
            $this->_putimage($smask);
        }
        // Palette
        if($info['cs']=='Indexed')
        {
            $filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
            $pal = ($this->compress) ? gzcompress($info['pal']) : $info['pal'];
            $this->_newobj();
            $this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
            $this->_putstream($pal);
            $this->_out('endobj');
        }
    }

    function _putxobjectdict()
    {
        foreach($this->images as $image)
            $this->_out('/I'.$image['i'].' '.$image['n'].' 0 R');
    }

    function _putresourcedict()
    {
        $this->_out('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
        $this->_out('/Font <<');
        foreach($this->fonts as $font)
            $this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
        $this->_out('>>');
        $this->_out('/XObject <<');
        $this->_putxobjectdict();
        $this->_out('>>');
    }

    function _putresources()
    {
        $this->_putfonts();
        $this->_putimages();
        // Resource dictionary
        $this->offsets[2] = strlen($this->buffer);
        $this->_out('2 0 obj');
        $this->_out('<<');
        $this->_putresourcedict();
        $this->_out('>>');
        $this->_out('endobj');
    }

    function _putinfo()
    {
        $this->_out('/Producer '.$this->_textstring('tFPDF '.tFPDF_VERSION));
        if(!empty($this->title))
            $this->_out('/Title '.$this->_textstring($this->title));
        if(!empty($this->subject))
            $this->_out('/Subject '.$this->_textstring($this->subject));
        if(!empty($this->author))
            $this->_out('/Author '.$this->_textstring($this->author));
        if(!empty($this->keywords))
            $this->_out('/Keywords '.$this->_textstring($this->keywords));
        if(!empty($this->creator))
            $this->_out('/Creator '.$this->_textstring($this->creator));
        $this->_out('/CreationDate '.$this->_textstring('D:'.@date('YmdHis')));
    }

    function _putcatalog()
    {
        $this->_out('/Type /Catalog');
        $this->_out('/Pages 1 0 R');
        if($this->ZoomMode=='fullpage')
            $this->_out('/OpenAction [3 0 R /Fit]');
        elseif($this->ZoomMode=='fullwidth')
            $this->_out('/OpenAction [3 0 R /FitH null]');
        elseif($this->ZoomMode=='real')
            $this->_out('/OpenAction [3 0 R /XYZ null null 1]');
        elseif(!is_string($this->ZoomMode))
            $this->_out('/OpenAction [3 0 R /XYZ null null '.sprintf('%.2F',$this->ZoomMode/100).']');
        if($this->LayoutMode=='single')
            $this->_out('/PageLayout /SinglePage');
        elseif($this->LayoutMode=='continuous')
            $this->_out('/PageLayout /OneColumn');
        elseif($this->LayoutMode=='two')
            $this->_out('/PageLayout /TwoColumnLeft');
    }

    function _putheader()
    {
        $this->_out('%PDF-'.$this->PDFVersion);
    }

    function _puttrailer()
    {
        $this->_out('/Size '.($this->n+1));
        $this->_out('/Root '.$this->n.' 0 R');
        $this->_out('/Info '.($this->n-1).' 0 R');
    }

    function _enddoc()
    {
        $this->_putheader();
        $this->_putpages();
        $this->_putresources();
        // Info
        $this->_newobj();
        $this->_out('<<');
        $this->_putinfo();
        $this->_out('>>');
        $this->_out('endobj');
        // Catalog
        $this->_newobj();
        $this->_out('<<');
        $this->_putcatalog();
        $this->_out('>>');
        $this->_out('endobj');
        // Cross-ref
        $o = strlen($this->buffer);
        $this->_out('xref');
        $this->_out('0 '.($this->n+1));
        $this->_out('0000000000 65535 f ');
        for($i=1;$i<=$this->n;$i++)
            $this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
        // Trailer
        $this->_out('trailer');
        $this->_out('<<');
        $this->_puttrailer();
        $this->_out('>>');
        $this->_out('startxref');
        $this->_out($o);
        $this->_out('%%EOF');
        $this->state = 3;
    }

    function _escapetext($s)
    {
        // Format a text string
        return $this->_escape($s);
    }

    function _escape($s)
    {
        // Add \ before \, ( and )
        return str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$s)));
    }
}

class Unicode
{
    function utf8_to_utf16be(&$txt, $bom = true)
    {
        if (!$txt) return false;
        if (!function_exists('mb_convert_encoding')) return false;
        $txt = mb_convert_encoding($txt, 'UTF-16BE', 'UTF-8');
        if ($bom) $txt = "\xFE\xFF".$txt;
        return $txt;
    }
}
