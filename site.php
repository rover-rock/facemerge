<?php
/**
 * 人脸融合模块微站定义
 *
 * @author leyao
 * @url http://bbs.we7.cc/
 */
if (!defined('IN_IA')) {
	exit('Access Denied');
}


class FacemergeModuleSite extends WeModuleSite
{
	public function doWebIndex($value='')
	{
		include $this->template('index');
	}
	public function doWebBg_manage()
	{
		global $_GPC,$_W;
		
		if($_W['isajax']){
			//上传图像

			$resdata=array(
			"code"=>0,
			"message"=>"",
			'imgurl'=>"",
			"imgname"=>"",
			"img_get"=>""
		); 
		$imgurl=$_FILES['file']['tmp_name'];
		$size=(int)$_FILES['file']['size']/1024;
		$suiji=$this->generate_password(6);
		$exename=substr(strrchr($_FILES['file']['name'], '.'), 1);
		$imy=$suiji.'.'.$exename;
		$imageSavePath ="../upload/".$imy;
		if(move_uploaded_file($imgurl, $imageSavePath)){
			$resdata['code']=0;
			$resdata['imgname']=$suiji;
			$resdata['imgurl']=$_SERVER['HTTP_HOST'].'/upload/'.$imy;
			
		}else{
			$resdata['code']=1;
		}

		//pdo_insert('face_bg',array('description'=>$_GPC['description'],'image'=>$resdata['imgurl']));
		return json_encode($resdata);
			
		}
		if($_W['ispost']){
			pdo_insert('face_bg',array('description'=>$_GPC['description'],'image'=>$_GPC['imgurl'],'modelid_left'=>$_GPC['modelid_left'],'modelid_right'=>$_GPC['modelid_right']));
		}
		if($_GPC['action']=='del'){
			pdo_delete('face_bg',array('id'=>$_GPC['id']));
		}


		$data=pdo_fetchall('select * from ims_face_bg ');
		$length=count($data);
		include $this->template('bg_manage');
	}

	public function doWebWord_manage()
	{
		global $_GPC,$_W;

		if($_GPC['action']=='add'){
			include $this->template('word_add');
		}
		elseif ($_GPC['action']=='save') {
			pdo_insert('face_word',array('word'=>$_GPC['word'],'age'=>intval($_GPC['age'])));
			$data=pdo_fetchall('select * from ims_face_word');
				include $this->template('word_manage');
		}
		elseif ($_GPC['action']=='edit'){
			pdo_fetch('face_word',array('word'=>$_GPC['word'],'age'=>intval($_GPC['age'])));
		}
		elseif($_GPC['action']=='del'){
			pdo_delete('face_word',array('id'=>$_GPC['id']));
			$data=pdo_fetchall('select * from ims_face_word');
		include $this->template('word_manage');
		}
		else{
			$data=pdo_fetchall('select * from ims_face_word');
		include $this->template('word_manage');
		}
	}

	public function doMobileFace()
	{

		                     
		include $this->template('face');
	}
	public function doMobileUpload()
	{
		global $_GPC,$_W;
		$resdata=array(
			"code"=>0,
			"message"=>"",
			'imgurl'=>"",
			"imgname"=>"",
			"img_get"=>""
		); 
		$imgurl=$_FILES['file']['tmp_name'];
		$size=(int)$_FILES['file']['size']/1024;
		$suiji=$this->generate_password(6);
		$exename=substr(strrchr($_FILES['file']['name'], '.'), 1);
		$imy=$suiji.'.'.$exename;
		$imageSavePath ="../upload/".$imy;
		
		if(move_uploaded_file($imgurl, $imageSavePath)){
			$resdata['code']=0;
			$resdata['imgname']=$suiji;
			$resdata['imgurl']=$_SERVER['HTTP_HOST'].'/upload/'.$imy;
			$resdata['img_get']=$this->merge($resdata['imgurl'])['img_url_thumb'];
			
		}else{
			$resdata['code']=1;
		}
		//dump破坏了返回值
		return json_encode($resdata);
	}
	public function generate_password( $length = 8 ) { 
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; 
		$password =""; 
		for ( $i = 0; $i < $length; $i++ ){  
			$password .= $chars[ mt_rand(0, strlen($chars) - 1) ]; 
		} 
		return $password; 
	}
	public function doMobileTest()
	{
		ini_set('default_socket_timeout', 1);
		return file_get_contents('http://shp.qpic.cn/tu_act_pic/0/NDQar2tew5k1/0');
	}
	public function merge($imgurl,$gender)	
	{
		global $_W,$_GPC;
		
		require('../addons/facemerge/vendor/TencentYoutuyun/Conf.php');
		require('../addons/facemerge/vendor/TencentYoutuyun/Youtu.php');
		require('../addons/facemerge/vendor/TencentYoutuyun/Auth.php');
		require('../addons/facemerge/vendor/TencentYoutuyun/Http.php');
		$appid='10117738';
    $secretId='AKIDNZWDepcTyDCvObsLp30h8JDrGSO4reXB';
    $secretKey='XopZ3i9LI2bv0qQr43nIemiT2NVP4Ugd';
    $userid='362463215';
    // $model_ids=array("﻿cf_fuwa_qiji","cf_fuwa_yasuiqian","cf_lover_fanli","cf_lover_libai","cf_lover_sunshang","cf_lover_wuque","cf_lover_xishi","cf_lover_yuhuan","cf_movie_baiqian","cf_movie_fengjiu");
    //$model_ids=array(array('male'=>'youtu_70821_20180131183947_7153','female'=>'youtu_70821_20180131183955_7154','location'=>0),array('male'=>'youtu_70821_20180131183825_7147','female'=>'youtu_70821_20180131183838_7148','location'=>0),array('female'=>'youtu_70821_20180131183753_7145','male'=>'youtu_70821_20180131183812_7146','location'=>1));
    $model_ids=array(array('male'=>'youtu_71338_20180205121719_7465','female'=>'youtu_71338_20180205121715_7464','location'=>1),array('male'=>'youtu_71338_20180205121731_7467','female'=>'youtu_71338_20180205121726_7466','location'=>1),array('male'=>'youtu_71338_20180205121748_7469','female'=>'youtu_71338_20180205121738_7468','location'=>1),array('male'=>'youtu_71338_20180205121759_7471','female'=>'youtu_71338_20180205121754_7470','location'=>1),array('male'=>'youtu_71338_20180205121804_7472','female'=>'youtu_71338_20180205121808_7473','location'=>0));
    $m=pdo_fetchcolumn('select modelid from ims_face_user where openid=:openid',array(':openid'=>$_W['openid']));
    if($m<0){
    	$i=rand(0,count($model_ids)-1);
   	pdo_update('face_user',array('modelid'=>$i),array('openid'=>$_W['openid']));
    }
    else{
    	$i=$m;
    }

   	
    \TencentYoutuyun\Conf::setAppInfo($appid, $secretId, $secretKey, $userid,\TencentYoutuyun\conf::API_YOUTU_END_POINT );
    $result = \TencentYoutuyun\YouTu::mergeface($imgurl,$model_ids[$i][$gender], 1);
    if(strlen($result['img_url'])<6){
    	$this->merge($imgurl,$gender);
    }
    //半张图片存入upload
    ini_set('default_socket_timeout', 1);
    $imagedata=imagecreatefromstring(file_get_contents($result['img_url']));

 //    $med=imagecreatetruecolor(150, 350);
	// imagecopyresized($med, $imagedata, 0, 0, 0, 0, 150, 350, imagesx($imagedata)	, imagesy($imagedata));
   
			//保存生成图片的文件名
			$suiji=$this->generate_password(6);
			$image_name='../upload/'.$suiji.'.png';
			imagepng($imagedata,$image_name);
			imagedestroy($imagedata);
			//将半张图片存入数据库中
			

			$n=pdo_fetchcolumn('select count(*) from ims_face_user where openid=:openid',array(':openid'=>$_W['openid']));
			if($n==0){
				if($gender=='male'){
				pdo_insert('face_user',array('openid'=>$_W['openid'],'image_boy'=>$image_name,'location'=>$model_ids[$i]['location']));}
				else{
					pdo_insert('face_user',array('openid'=>$_W['openid'],'image_girl'=>$image_name,'location'=>$model_ids[$i]['location']));
				}

			}
			else
			{
				if($gender=='male'){
				pdo_update('face_user',array('image_boy'=>$image_name,'location'=>$model_ids[$i]['location']),array('openid'=>$_W['openid']));}
				else{
					pdo_update('face_user',array('image_girl'=>$image_name,'location'=>$model_ids[$i]['location']),array('openid'=>$_W['openid']));
				}
			}
    		
    return $result;
	}
	//生成二维码图片
	public function qrcode()
	{
		$faceUrl=$this->createMobileUrl('face');
		$url= "http://".$_SERVER['HTTP_HOST'].substr($faceUrl,1,strlen($faceUrl)-1);
		//load->func('logging');
		require('../addons/facemerge/vendor/phpqrcode.php');
		$value = $url;                  //二维码内容  
      
    	$errorCorrectionLevel = 'L';    //容错级别   
    	$matrixPointSize = 4;           //生成图片大小    
      
    //生成二维码图片  
    $filename = '../qrcode/'.microtime().'.png';  
    QRcode::png($value,$filename , $errorCorrectionLevel, $matrixPointSize, 2);    
    
    // $QR = $filename;                //已经生成的原始二维码图片文件    
  
  
    // $QR = imagecreatefromstring(file_get_contents($QR));    
    // //输出图片    
    // imagepng($QR, 'qrcode.png');    
    // imagedestroy($QR);  
    // return '<img src="qrcode.png" alt="使用微信扫描支付">';     
    return $filename;
	}
	public function doMobileChangeModel()
	{
		global $_GPC;
		
		return $this->merge($_GPC['img_url'],$_GPC['gender'])['img_url'];
	}
	public function doMobileMerge()
	{
		
		global $_GPC,$_W;
		 
		pdo_update('face_user',array('modelid'=>-1),array('openid'=>$_W['openid']));
		$data=pdo_fetch('select * from ims_face_user where openid=:openid',array(':openid'=>$_W['openid']));
		if($data['location']==0){
		$ii1=file_get_contents($data['image_boy']);//20s
		$ii2=file_get_contents($data['image_girl']);//17s
		}
		else{
			$ii2=file_get_contents($data['image_boy']);//20s
		$ii1=file_get_contents($data['image_girl']);//17s
		}
		$img1=imagecreatefromstring($ii1);
		$img2=imagecreatefromstring($ii2);
		//$img3=imagecreatefromstring(file_get_contents($this->qrcode()));
		$img4=imagecreatefromstring(file_get_contents('../addons/facemerge/template/mobile/footer.jpg'));
		$img5=imagecreatefromstring(file_get_contents('../addons/facemerge/template/mobile/header.jpg'));
		$isize1w=imagesx($img1);
		$isize1h=imagesy($img1);
		$isize2w=imagesx($img2);
		$isize2h=imagesy($img2);
		//$isize3w=imagesx($img3);
		//$isize3h=imagesy($img3);
		$isize4w=imagesx($img4);
		$isize4h=imagesy($img4);
		$isize5w=imagesx($img5);
		$isize5h=imagesy($img5);
		$width=$isize1w+$isize2w;	                                              //图像宽度
 		$height=$isize1h>$isize2h?$isize1h:$isize2h;                                             //图像高度
  $img=imagecreatetruecolor($width,$height+$isize4h+80-300);  
  $white=imagecolorallocate($img,255,255,255);             //白色
 	imagefill($img,0,0,$white);                              //将背景设置为白色
 //imageline($img,20,20,260,150,$red);                      //画出一条红色的线
 //imagestring($img,5,50,50,"hello,world!!",$blue);   
  $black=imagecolorallocate($img,0,0,0); 
  	imagecopyresized($img, $img5, 0, 0, 0, 0, $width, 80, $isize5w, $isize5h);
 	imagecopy($img, $img1, 0, 80, 0, 0, $isize1w,$isize1h);
 	imagecopy($img, $img2, $isize1w,80, 0, 0, $isize2w,$isize2h);
 	imagecopyresized($img,$img4,0,$isize1h+80,0,0,$width,$isize4h-300,$isize4w,$isize4h);
 	//imagecopy($img,$img3,10,20+$isize1h+80,0,0,$isize3w,$isize3h);
 	//imagestring($img, 5, $isize3w,$isize1h, 'this is our whole story...please shakeit ..sjdifjsia', $black);
 //header("content-type: image/png");                    //输出图像的MIME类型
	$suiji=$this->generate_password(6);
	$image_name='../upload/'.$suiji.'.png';
	pdo_update('face_user',array('image_result'=>$image_name),array('openid'=>$_W['openid']));
 imagepng($img,$image_name);     //这种输出方式，存在编码问题 base64?                                //输出一个PNG图像数据
 imagedestroy($img);  
 imagedestroy($img1);  
 imagedestroy($img2);
 imagedestroy($img4);
 imagedestroy($img5);  
 return $image_name;

	}
}


?>