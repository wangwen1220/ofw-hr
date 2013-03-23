<?php
 /*
 * 74cms �ļ��ϴ�
|   @param: $dir      -- ���Ŀ¼,����"/" [�ִ�] 
|   @param: $file_var -- ������ [�ִ�] 
|   @param: $max_size -- �趨����ϴ�ֵ,��kΪ��λ. [����/������] 
|   @param: $type     -- �޶������(Сд)�������"/"����,���޶������� [�ִ�] 
|   @param: $name     -- �ϴ�������,������Ϊԭ��,trueΪϵͳ������� [����ֵ] 
|   return: �ϴ����ļ���
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/ 
function _asUpFiles($dir, $file_var, $max_size='', $type='', $name=false) 
{
if (!file_exists($dir)) adminmsg("�ϴ�ͼƬʧ�ܣ��ϴ�Ŀ¼ ".$dir." ������!",0);
if (!is_writable($dir)) 
{
adminmsg("�ϴ�ͼƬʧ�ܣ��ϴ�Ŀ¼ ".$dir." �޷�д��!",0);
exit(); 
}
$upfile=& $_FILES["$file_var"]; 
$upfilename =  $upfile['name']; 
if (!($upfilename==='')) 
{ 
if (!is_uploaded_file($upfile['tmp_name'])) 
{ 
adminmsg('�ϴ�ͼƬʧ�ܣ���ѡ����ļ��޷��ϴ�',0);
exit(); 
} 
if ($max_size>0 && $upfile['size']/1000>$max_size) 
{ 
adminmsg("�ϴ�ͼƬʧ�ܣ��ļ���С���ܳ���  ".$max_size."KB",0);
exit(); 
} 
$ext_name = strtolower(str_replace(".", "", strrchr($upfilename, "."))); 
if (!($type==='') && strpos($type, $ext_name)===false) 
{ 
adminmsg("�ϴ�ͼƬʧ�ܣ�ֻ�����ϴ� ".$type." ���ļ���",0);
exit(); 
}
($name==true)?$uploadname=time().mt_rand(100,999).".".$ext_name :'';
($name==false)?$uploadname=$upfilename:'';
!is_bool($name)?($uploadname=$name.".".$ext_name):'';
//$uploadname = $name ? md5(uniqid(rand())).".".$ext_name : $upfilename; 
if (!move_uploaded_file($upfile['tmp_name'], $dir.$uploadname)) 
{ 
adminmsg('�ϴ�ͼƬʧ�ܣ��ļ��ϴ�����',0);
 exit(); 
} 
return $uploadname; 
} 
else 
{ 
return ''; 
} 
} 
/*ͼ�����Ժ���
����˵����
$srcfile ԭͼ��ַ�� 
$dir  ��ͼĿ¼ 
$thumbwidth ��Сͼ�����ߴ� 
$thumbheitht ��Сͼ�����ߴ� 
$ratio Ĭ�ϵȱ������� Ϊ1����С���̶��ߴ硣 
*/ 
function makethumb($srcfile,$dir,$thumbwidth,$thumbheight,$ratio=0)
{ 
 //�ж��ļ��Ƿ���� 
if (!file_exists($srcfile))return false;
 //�����µ�ͬ���ļ�����Ŀ¼��ͬ 
$imgname=explode('/',$srcfile); 
$arrcount=count($imgname); 
$dstfile = $dir.$imgname[$arrcount-1]; 
//����ͼ��С 
$tow = $thumbwidth; 
$toh = $thumbheight; 
if($tow < 40) $tow = 40; 
if($toh < 45) $toh = 45;    
 //��ȡͼƬ��Ϣ 
    $im =''; 
    if($data = getimagesize($srcfile)) { 
        if($data[2] == 1) { 
            $make_max = 0;//gif������ 
            if(function_exists("imagecreatefromgif")) { 
                $im = imagecreatefromgif($srcfile); 
            } 
        } elseif($data[2] == 2) { 
            if(function_exists("imagecreatefromjpeg")) { 
                $im = imagecreatefromjpeg($srcfile); 
            } 
        } elseif($data[2] == 3) { 
            if(function_exists("imagecreatefrompng")) { 
                $im = imagecreatefrompng($srcfile); 
            } 
        } 
    } 
    if(!$im) return ''; 
    $srcw = imagesx($im); 
    $srch = imagesy($im); 
    $towh = $tow/$toh; 
    $srcwh = $srcw/$srch; 
    if($towh <= $srcwh){ 
        $ftow = $tow; 
        $ftoh = $ftow*($srch/$srcw); 
    } else { 
        $ftoh = $toh; 
        $ftow = $ftoh*($srcw/$srch); 
    } 
    if($ratio){ 
        $ftow = $tow; 
        $ftoh = $toh; 
    } 
    //��СͼƬ 
    if($srcw > $tow || $srch > $toh || $ratio) { 
        if(function_exists("imagecreatetruecolor") && function_exists("imagecopyresampled") && @$ni = imagecreatetruecolor($ftow, $ftoh)) { 
            imagecopyresampled($ni, $im, 0, 0, 0, 0, $ftow, $ftoh, $srcw, $srch); 
        } elseif(function_exists("imagecreate") && function_exists("imagecopyresized") && @$ni = imagecreate($ftow, $ftoh)) { 
            imagecopyresized($ni, $im, 0, 0, 0, 0, $ftow, $ftoh, $srcw, $srch); 
        } else { 
            return ''; 
        } 
        if(function_exists('imagejpeg')) { 
            imagejpeg($ni, $dstfile); 
        } elseif(function_exists('imagepng')) { 
            imagepng($ni, $dstfile); 
        } 
    }else { 
        //С�ڳߴ�ֱ�Ӹ��� 
    copy($srcfile,$dstfile); 
    } 
    imagedestroy($im); 
    if(!file_exists($dstfile)) { 
        return ''; 
    } else { 
        return $dstfile; 
    } 
}

/**
 * ��������ͼ
 * @param string $srcName
 * @param int $newWidth
 * @param int $newHeight
 * @param string $newName
 */
function resizeImg($srcName, $newName, $newWidth, $newHeight, $cut = false) {

	 //�ж��ļ��Ƿ���� 
	if (!file_exists($srcName))return false;
	 //�����µ�ͬ���ļ�����Ŀ¼��ͬ 
	$imgname=explode('/',$srcName); 
	$arrcount=count($imgname); 
	$newName = $newName.$imgname[$arrcount-1]; 
	
	//��ȡͼƬ��Ϣ
	$info = '';
	$data = getimagesize($srcName, $info);
	
	switch ($data[2]) {
		case 1:
			if (function_exists("imagecreatefromgif")) {
				$im = imagecreatefromgif($srcName);
			} else {
				die('���GD�ⲻ��ʹ��GIF��ʽ��ͼƬ');
			}
			break;
		case 2:
			if (function_exists("imagecreatefromjpeg")) {
				$im = imagecreatefromjpeg($srcName);
			} else {
				die('���GD�ⲻ��ʹ��jpeg��ʽ��ͼƬ');
			}
			break;
		case 3:
			$im = imagecreatefrompng($srcName);
			break;
		default:
			return false;
		break;
	}	
	$srcW = imagesx($im);#���
	$srcH = imagesy($im);#�߶�
	
	//�ü�
	if ($cut) {
		//���ź�ĳߴ�
		$newRate = $newWidth / $newHeight;
		$srcRate = $srcW / $srcH;
		if ($newRate <= $srcRate) {
			$toH = $newHeight;
			$toW = $toH * ($srcW / $srcH);
		} else {
			$toW = $newWidth;
			$toH = $toW * ($srcH / $srcW);
		}
	} else {
		//���ź�ĳߴ�
		$newRate = $newWidth / $newHeight;
		$srcRate = $srcW / $srcH;
		if ($newRate <= $srcRate) {
			$toW = $newWidth;
			$toH = $toW * ($srcH / $srcW);
		} else {
			$toH = $newHeight;
			$toW = $toH * ($srcW / $srcH);
		}
	}
	
	
	//��ʼ����
	if ($srcW > $newWidth || $srcH > $newHeight) {
		if (function_exists("imagecreatetruecolor")) {
			@$ni = imagecreatetruecolor($toW, $toH);
			if ($ni) {
				imagecopyresampled($ni, $im, 0, 0, 0, 0, $toW, $toH, $srcW, $srcH);
			} else {
				$ni = imagecreate($toW, $toH);
				imagecopyresampled($ni, $im, 0, 0, 0, 0, $toW, $toH, $srcW, $srcH);
			}
		} else {
			$ni = imagecreate($toW, $toH);
			imagecopyresampled($ni, $im, 0, 0, 0, 0, $toW, $toH, $srcW, $srcH);			
		}
		
		
		//ͼƬ�ü�
		if ($cut) {			
			$toW = min($newWidth, $toW);
			$toH = min($newHeight, $toH);
			@$ni2 = imagecreatetruecolor($newWidth, $toH);
			imagecopy ( $ni2, $ni, 0, 0, 0, 0, $toW, $toH);
			
			if (function_exists("imagejpeg")) {
				imagejpeg($ni2, $newName);
			} else {
				imagepng($ni2, $newName);
			}
			
			imagedestroy($ni);
			imagedestroy($ni2);
		} else {			
			if (function_exists("imagejpeg")) {
				imagejpeg($ni, $newName);
			} else {
				imagepng($ni, $newName);
			}
			
			imagedestroy($ni);
		}		
	}
	
	imagedestroy($im);
}
?>