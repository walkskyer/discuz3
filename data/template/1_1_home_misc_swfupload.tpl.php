<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); if(!empty($uploadResponse)) { ?>
<uploadResponse>
<message><?php if($status=="success") { ?>完成<?php } else { ?><?php echo $uploadfiles;?><?php } ?></message>
<status><?php echo $status;?></status>
<proid><?php echo $proid;?></proid>
<albumid><?php echo $albumid;?></albumid>
<picid><?php echo $picid;?></picid>
<?php if($fileurl) { ?><filepath><?php echo $fileurl;?></filepath><?php } ?>
</uploadResponse>
<?php } else { ?>
<parameter>
<?php if($iscamera) { ?>
<images><?php if(is_array($dirarr)) foreach($dirarr as $key => $val) { ?><categories name="<?php echo $val['0'];?>" directory="<?php echo $val['1'];?>"><?php if(is_array($val['2'])) foreach($val['2'] as $ikey => $value) { ?><img name="<?php echo $value;?>"/>
<?php } ?>
</categories>
<?php } ?>
</images>
<?php } elseif($isdoodle) { ?>
<background><?php if(is_array($filearr)) foreach($filearr as $key => $filename) { ?><bg url="<?php echo STATICURL;?>image/doodle/big/<?php echo $filename;?>" thumb="<?php echo STATICURL;?>image/doodle/thumb/<?php echo $filename;?>"/>
<?php } ?>
</background>
<?php } elseif($isupload) { ?>
<allowsExtend>
<extend depict="All Image File(*.jpg,*.jpeg,*.gif,*.png)">*.jpg,*.gif,*.png,*.jpeg</extend>
</allowsExtend>
<?php } ?>
<language>
<create>创建</create>
<notCreate>取消</notCreate>
<albumName>相册名</albumName>
<createTitle>创建新相册</createTitle>
<categoryDesc>相册分类</categoryDesc>
<categoryPrompt>请选择相册分类</categoryPrompt>
<?php if(!$isdoodle) { ?>
<okbtn>继续</okbtn>
<cancelbtn>查看</cancelbtn>
<?php if($isupload) { ?>
<fileName>文件名</fileName>
<depict>描述(单击修改)</depict>
<size>文件大小</size>
<stat>上传进度</stat>
<aimAlbum>上传到:</aimAlbum>
<browser>浏览</browser>
<delete>删除</delete>
<upload>上传</upload>
<okTitle>上传完成</okTitle>
<okMsg>所有文件上传完成!</okMsg>
<uploadTitle>正在上传</uploadTitle>
<uploadMsg1>总共有</uploadMsg1>
<uploadMsg2>个文件等待上传,正在上传第</uploadMsg2>
<uploadMsg3>个文件</uploadMsg3>
<bigFile>文件过大</bigFile>
<uploaderror>上传失败</uploaderror>
<?php } elseif($iscamera) { ?>
<desultory>抓拍</desultory>
<series>连拍</series>
<save>保存</save>
<pageup>上一页</pageup>
<pagedown>下一页</pagedown>
<clearbg>清除相框</clearbg>
<reload>重载</reload>
<cancel>取消</cancel>
<siteerror>参数错误,系统载入失败</siteerror>
<ver1>程序需 FlashPlayer 9.0.45 以上版本您的播放器版本为</ver1>
<ver2>请升级</ver2>
<refuse>在您的机器上检测到摄象头但您拒绝了摄象头的使用</refuse>
<countdown>倒数</countdown>
<second>秒</second>
<nocam>在您的机器上没有检测到摄象头或者您的摄象头设备正在使用中</nocam>
<autoShooting>秒自拍</autoShooting>
<explain>参数设置:</explain>
<okTitle>上传完成</okTitle>
<okMsg>大头贴上传完成</okMsg>
<saveTitle>正在上传</saveTitle>
<saveToNote>保存到</saveToNote>
<saveMsg1>总共有</saveMsg1>
<saveMsg2>张大头贴,正在保存第</saveMsg2>
<saveMsg3>张大头贴</saveMsg3>
<?php } } else { ?>
<reload>重画</reload>
<save>保存</save>
<notDraw>没有任何涂鸦动作，无法保存</notDraw>
<?php } ?>
</language>
<config>
<userid><?php echo $_G['uid'];?></userid>
<hash><?php echo $hash;?></hash>
<maxupload><?php echo $max;?></maxupload>
<uploadurl><?php echo $uploadurl;?></uploadurl>
<feedurl><?php echo $feedurl;?></feedurl>
<albumurl><?php echo $albumurl;?></albumurl>
<categoryStat><?php echo $categorystat;?></categoryStat>
<categoryRequired><?php echo $categoryrequired;?></categoryRequired>
<?php if($iscamera) { ?>
<countdown>3</countdown>
<countBy>2000</countBy>
<?php } ?>
</config>
<?php if($isdoodle) { ?>
<filters>
<filter id="0">禁用</filter>
<filter id="1">阴影</filter>
<filter id="2">模糊</filter>
<filter id="3">发光</filter>
<filter id="4">水彩</filter>
<filter id="5">喷溅</filter>
<filter id="6">布纹</filter>
</filters>
<?php } ?>
<albums>
<album id="-1">请选择相册</album><?php if(is_array($albums)) foreach($albums as $key => $value) { ?><album id="<?php echo $value['albumid'];?>"><?php echo $value['albumname'];?></album>
<?php } ?>
<album id="add">+创建新相册</album>
</albums>
<?php if($_G['setting']['albumcategorystat'] && $categorys) { ?>
<categorys>
<category catid="0">选择分类</category><?php if(is_array($categorys)) foreach($categorys as $key => $value) { if($value['level'] == 0) { ?>
<category catid="<?php echo $key;?>"><?php echo $value['catname'];?></category><?php if(is_array($value['children'])) foreach($value['children'] as $catid) { ?><category catid="<?php echo $categorys[$catid]['catid'];?>">--<?php echo $categorys[$catid]['catname'];?></category><?php if(is_array($categorys[$catid]['children'])) foreach($categorys[$catid]['children'] as $catid2) { ?><category catid="<?php echo $categorys[$catid2]['catid'];?>">----<?php echo $categorys[$catid2]['catname'];?></category>
<?php } } } } ?>
</categorys>
<?php } ?>
</parameter>
<?php } ?>