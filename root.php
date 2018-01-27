<?php
//----------------------------------------------
//                read ffdeatail read
//----------------------------------------------
function dir_read($dir)
{
 if(is_dir($dir))
 {
  $dir=realpath ($dir);
  $list=[];
  $fflist=array_diff(scandir($dir),['.','..']);
  foreach($fflist as $ff)
  {
  $list[]=(object)['name'=>$ff,'size'=>filesize($dir.'/'.$ff),'type'=>filetype($dir.'/'.$ff),'atime'=>fileatime($dir.'/'.$ff),'mtime'=>fileatime($dir.'/'.$ff)];
  }
  clearstatcache();
  return $list;
 }
 else return -1;
}
//-------------------------------------------------
//                 folder create
//-------------------------------------------------
function folder_create($dir,$name)
{
 return mkdir($dir.'/'.$name);
}
//-------------------------------------------------
//                 folder_size
//-------------------------------------------------
function folder_size($dir)
{
 $dir=realpath($dir);
 if(file_exists($dir))
 {
  $size=0;
  $fflist=array_diff(scandir($dir),['.','..']);
  foreach($fflist as $ff)
  {
  if(is_dir($dir.'/'.$ff))$size+=folder_size($dir.'/'.$ff);
  else $size+=filesize($dir.'/'.$ff);
  }
  return $size + filesize($dir);
 }
 else return 0;
}
//-------------------------------------------------
//                  folder_copy
//-------------------------------------------------
function folder_copy($from,$to)
{
 if(strripos(realpath($from),'/'))
 $fns=explode('/',realpath($from));
 else
 $fns=explode('\\',realpath($from));
 
 var_dump($fns);
 $fname=$fns[count($fns)-1];
 $fflist=array_diff(scandir($from),['.','..']);
 mkdir($to.'/'.$fname);
 echo '-->'.$to.'/'.$fname.'<--';
 foreach($fflist as $ff)
 {
  if(is_dir($from.'/'.$ff))folder_copy($from.'/'.$ff,$to.'/'.$fname);
  else copy($from.'/'.$ff,$to.'/'.$fname.'/'.$ff);
 }
}
//-------------------------------------------------
//                  folder_delete
//-------------------------------------------------
function folder_delete($dir)
{
 $dir=realpath($dir);
 if(file_exists($dir))
 {
  $fflist=array_diff(scandir($dir),['.','..']);
  foreach($fflist as $ff)
  {
  if(is_dir($dir.'/'.$ff))folder_delete($dir.'/'.$ff);
  else unlink($dir.'/'.$ff);
  }
  rmdir($dir);
  return 1;
 }
 else  return 0;
}
//--------------------------------------------------
//                  list_copy
//-------------------------------------------------
function list_copy($dir,$fflist,$to)
{
 $dir=realpath($dir);
 $to= realpath($to);
 if(is_dir($dir)&&is_dir($to))
 {
  foreach($fflist as $ff)
  {
  if(is_dir($dir.'/'.$ff))folder_copy($dir.'/'.$ff,$to);
  else copy($dir.'/'.$ff,$to.'/'.$ff);
  }
  return 1;
 }
 else  return 0;
}
//------+---------------------------------------
//                 list_move
//----------------------------------------------
function list_move($dir,$fflist,$to)
{
 $dir=realpath($dir);
 $to =realpath($to);
 if(is_dir($dir)&&is_dir($to))
 {
  foreach($fflist as $ff)
  {
  if(file_exists($dir.'/'.$ff))
  rename($dir.'/'.$ff,$to.'/'.$ff);
  }
  return 1;
 }
 else  return 0;

}
//---------------------------------------------//---
//                list_delete
//------------------------------------------------
function list_delete($dir,$fflist)
{
 $dir=realpath($dir);
 echo $dir;
 if(is_dir($dir))
 {
  var_dump($dir);
  print_r($fflist);
  foreach($fflist as $ff)
  {
   if(file_exists($dir.'/'.$ff))
   {
   if(is_dir($dir.'/'.$ff))folder_delete($dir.'/'.$ff);
    else unlink($dir.'/'.$ff);
   }
  }
  return 1;
 }
 else  return 0;
}
//-------------------------------------------------
//                 ffrename
//-------------------------------------------------
function ffrename($dir,$name,$newname)
{
 if(file_exists($dir.'/'.$name))
 {
  return rename($dir.'/'.$name,$dir.'/'.$newname);
 }
 else return 0;
}
//--------------------------------------------------
//                  detail
//--------------------------------------------------
function ffdetail($dir,$ffname)
{ 
 $dir=realpath($dir);
 if(file_exists($dir.'/'.$ffname))
 {
  if(is_dir($dir.'/'.$ffname))
  {
   $list[]=(object)['name'=>$ffname,'size'=>folder_size($dir.'/'.$ffname),'type'=>filetype($dir.'/'.$ffname),'atime'=>fileatime($dir.'/'.$ffname),'mtime'=>fileatime($dir.'/'.$ffname)];
  
  clearstatcache();
  return $list;
  }
  else
  {
     $list[]=(object)['name'=>$ffname,'size'=>filesize($dir.'/'.$ffname),'type'=>filetype($dir.'/'.$ffname),'atime'=>fileatime($dir.'/'.$ffname),'mtime'=>fileatime($dir.'/'.$ffname)];
  
  clearstatcache();
  return $list;
  }
  
 }
 else return 0;
}
//---------------------------//------------------------
//                    download
//---------------------------------------------------
$id=1;
function zipper($dir,$list,$name)
{
 $dir=realpath($dir);
 if(!$list)
 $list=array_diff(scandir($dir),['.','..']);
 $zip =new ZipArchive();
 if($zip->open($GLOBALS['zippath'].'/'.$name, ZIPARCHIVE::CREATE)!== TRUE){return 0;}
 foreach($list as $f)
 {
  if(file_exists($dir.'/'.$f))
  {
   if(is_dir($dir.'/'.$f))
   $zip->addFile($GLOBALS['zippath'].'/'.zipper($dir.'/'.$f,null,++$GLOBALS['progress'].'.zip'),$f.'.zip');
   else
   $zip->addFile($dir.'/'.$f,$f);
  }
 }
 if(!$zip->close())
 {var_dump($zip);}
 return $name;
}

function download($dir,$list=null)
{
 
 $dir=realpath($dir);
 $sname=microtime();
 $GLOBALS['zippath']='./../ziptemp';
 @mkdir($GLOBALS["zippath"]);
 $GLOBALS['zippath']='./../ziptemp/'.$GLOBALS['id'];
 @mkdir($GLOBALS["zippath"]);
 $GLOBALS['zippath']='./../ziptemp/'.$GLOBALS['id'].'/'.$sname;
 @mkdir($GLOBALS["zippath"]);
 
 $GLOBALS['progress']=0;
 $name=zipper($dir,$list,$sname.".zip");
  if(file_exists($GLOBALS['zippath'].'/'.$name))
  {
  header('Content-Description : File Transfer');
  header('Content-Disposition : attachment; filename='.$name);
  header('Content-Type:application/zip'); 
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: '.filesize($GLOBALS['zippath'].'/'.$name));
  readfile($GLOBALS['zippath'].'/'.$name);
 }
 folder_delete($GLOBALS['zippath']);
}
//----------------------------------------------------
//                 unzipper
//----------------------------------------------------
function unzipper($dir,$name)
{
	if(!file_exists($dir.'/'.$name))return 0;
	$zip = new ZipArchive;
	$fname=@explode('.',$name);
	if(!($fname[1]=='zip'))return "it is not a zip file";
	while(1)
	{
		if(file_exists($dir.'/'.$fname[0]))
		$fname[0].='copy';
		else break;
	}
	if(!@mkdir($dir.'/'.$fname[0]))return "unable to create folder";
	if (@$zip->open($dir.'/'.$name)===true) {
		$zip->extractTo($dir.'/'.$fname[0]);
		$zip->close();
		return '1';
	} else {
		return '0';
	}
}
//----------------------------------------------------
//                 search
//----------------------------------------------------
function search($text)
{
 $a='';
 $text=explode(' ',$text);
 var_dump($text);
 for($i=0;$i<count($text)-1;$i++)
 $a.=$text[$i].'|';
 $a.=$text[count($text)-1];
 echo $a;
$dir=realpath ("./");
$directory =new RecursiveDirectoryIterator($dir);
$flattened =new RecursiveIteratorIterator($directory);
$files =new RegexIterator($flattened,'/(.*)([\/])([a-zA-Z0-9]*)('.$a.')([^\/]*)/');
 return iterator_to_array($files);
}
//----------------------------------------------------
//                 upload
//----------------------------------------------------
function upload($dir)
{
 if(@$_FILES["files"])
 {
	for($i=0;$i<count(@$_FILES["files"]['name']);$i++)
	{
		if(file_exists($dir.'/'.$_FILES["files"]['name'][$i]))
		move_uploaded_file($_FILES['files']['tmp_name'][$i],$dir.'/'.time().$_FILES['files']['name'][$i]);
		else
		move_uploaded_file($_FILES['files']['tmp_name'][$i],$dir.'/'.$_FILES['files']['name'][$i]);
	}
	 echo "sucessfuly uploaded";
  }
 else "no file to upload";
}
//-----------------------------------
//                choices
//-----------------------------------
session_start();
if(isset($_SESSION['id']) && @$_SESSION['id']=='jk@rf3u7845i7j!&*k34@9dj3'&& (@$_REQUEST['req']||@$_FILES) )
{
	chdir('../');
	if(!@$_REQUEST['req'])$_REQUEST['req']=@$_SERVER["HTTP_ETAG"];
	$req=json_decode($_REQUEST['req']);
	switch(@$req->actionname)
	{
	 case   "view":echo json_encode($a=dir_read($req->directory));break;
	 case "delete":echo list_delete($req->directory,$req->fflist);break;
	 case "move":list_delete($req->to,$req->fflist);
	 echo list_move($req->directory,$req->fflist,$req->to);break;
	 case "copy":list_delete($req->to,$req->fflist); echo list_copy($req->directory,$req->fflist,$req->to);break;
	 case "createfolder":echo folder_create($req->directory,$req->foldername);break;
	 case "ffdetail": print_r(ffdetail($req->directory, $req->ffname));break;
	 case "download":download($req->directory,$req->fflist);break;
	 case "rename":echo ffrename($req->directory,$req->name,$req->newname);break;
	 case "search": var_dump(search($req->search));break;
	 case "upload": echo upload($req->directory);break;
	 case "unzip": echo unzipper($req->directory,$req->name);break;
	}
	exit();

}
//------------------------------------------------
//                  start cloud
//-------------------------------------------------
if(!(@$_POST['username']=='root' && @$_POST['password']=='root'))
{
echo "<style>body{width: 280px;margin: auto;margin-top: 107px;}input{width: 275px;display: block;outline: 0px;height: 28px;text-indent: 10px;border: 1px solid #CCC;margin-top: 12px;color: #121030;font-size: 15px;box-shadow: 0 0 2px 0px #CCC inset;}</style><div><form method='POST'><input name='username'placeholder='Username' ><input type='password' name='password' placeholder='password' ><input type='submit' name='submit' value='login'></form></div>";exit();
}
else
{
$_SESSION['id']="jk@rf3u7845i7j!&*k34@9dj3";
}


?>
<html>
<head>
  <title>Home</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<style>
#directorypath span:active, .dir-item:active {
color: #F60;
background-color: #ECECEC;
}
#directorypath span:hover, .dir-item:hover {
color: #003DFF;
}
#directorypath span, .dir-item {
cursor: pointer;
}
button, input[type=button] {
    width: 74px;
    /* margin-bottom: auto; */
    display: inline-block;
    /* padding: 1px 15px; */
    margin-bottom: 1px;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
    color: #fff;
    background-color: #337ab7;
    border-color: #2e6da4;
}
hr {
    border: 0px;
    border-top: 1px solid #ccc;
}
div#directorypath {
    font-weight: 800;
}
</style>
</head>
<body class=>
<div id='header'>
<input type="search " id="search" placeholder='search'/>
<input id='searchbtn'type='button' value='search'/>
</div>
<div id='body'>
<hr/>
<div>
<button id='cut'>cut</button>
<button id='copy'>copy</button>
<button id='paste'>paste</button>
<button id='unlink'>delete</button>
<button id='ffrename'>rename</button>
<button id='createfolder'>create</button>
<button id='detail'>detail</button>
<button id='unzip'>unzip</button>
<button id='download'>download</button>
</div>
 
<hr>
<div id='directorypath'></div>
<hr>
<div id='directory'></div>
<hr>
<div  id='footer'>
<form>
<input id='inputfile' type='file' multiple  />
<input id='uploadbtn'type='button' value='upload'/>
</form>
</div> 
<hr>
<button onclick='$(".info").html("");'>clear</button>
<div class='info'></div>
<div class='impinfo'></div>
</body>
  <script>
  var URL = window.URL || window.webkitURL;
window.Blob&&(Blob.prototype.slice=Blob.prototype.slice||Blob.prototype.mozSlice||Blob.prototype.webkitSlice);window.File&&(File.prototype.slice=File.prototype.slice||File.prototype.mozSlice||File.prototype.webkitSlice);
cloud=
{
  connection:{},
     account:{},
     profile:{},
   clipboard:null
};
//---------------------------------------------------
//                      link setup
//---------------------------------------------------
cloud.connection.root='/';
cloud.connection.requestto=window.location.origin+window.location.pathname;
cloud.connection.cwd='.';
//---------------------------------------------------
//                      request
//---------------------------------------------------
cloud.request={};
cloud.request.dir=function(dirname)
{
 
 if(dirname===undefined)
 dirname=cloud.connection.root;
 
 requestobj=
   {
   actionname:'view',
    directory:dirname
   };
 var requeststr=JSON.stringify(requestobj);
  $.post(cloud.connection.requestto,{req:requeststr},function(data,status) {if(status=="success"){
  data=JSON.parse(data);if(typeof(data)=='object')
  {cloud.connection.cwd=dirname;cloud.display.dir(data);}else $('.info').html(data+status);}else alert(status);});
}
cloud.request.dir();

cloud.request.cut=function()
{
 var requestobj=
 {
 actionname:'move',
  directory:cloud.connection.cwd,
     fflist:cloud.selector()
 };
 if(requestobj.fflist.length)
 cloud.clipboard=requestobj;

}
//--------
cloud.request.copy =function()
{
 var requestobj=
 {
 actionname:'copy',
  directory:cloud.connection.cwd,
     fflist:cloud.selector()
 };
 if(requestobj.fflist.length)
 cloud.clipboard=requestobj;
}
//-----------
cloud.request.paste =function()
{
 
 if(cloud.clipboard.actionname)
 {

 var requestobj=cloud.clipboard;
     requestobj.to=cloud.connection.cwd;
     temp=array_common(requestobj.fflist,cloud.dirread());
     
     

 if(requestobj.directory==requestobj.to)
 {alert("can't copy in same directory");return;}
 
 var flag=0;
 if(requestobj.actionname=='move')
 {
  for(var i=0;i<requestobj.fflist.length;i++){
  if(requestobj.to.indexOf(requestobj.directory+'/'+requestobj.fflist[i])!=-1)
  { flag=1; break;}}
  
 }
 if(flag)
 {alert("can't move in its own directory"); return;}
  
 
 
 if(temp.length)
 { 
  if(!confirm('over write existing file'))
  {
  if(confirm('skip existing file'))
  requestobj.fflist=array_diff(requestobj.fflist,temp);
  else {alert('cancel done');return;}
  }
 }
 
 
 
  
 var requeststr=JSON.stringify(requestobj);
 $.post(cloud.connection.requestto,{req:requeststr},function(data,status) {$('.info').html(data+status);cloud.request.dir(cloud.connection.cwd);});
 
 }

}
//-----------*/
cloud.selector= function()
{
temp=$("#directory :checked").closest("div").map(function(){return $(this).attr('name');}).get();
return temp;
}

cloud.dirread= function()
{
temp=$("#directory input").closest("div").map(function(){return $(this).attr('name');}).get();
return temp;
}

array_common =function(arr1,arr2)
{
 var common=[];
 for(var i=0;i<arr1.length;i++)
 for(var j=0;j<arr2.length;j++)
 if(arr1[i]==arr2[j])
 common[common.length]=arr1[i];
 return common;
}

array_diff =function(arr1,arr2)
{
 var diffarr=[];
 var diff=1;
 for(var i=0;i<arr1.length;i++)
 {
  diff=1;
  for(var j=0;j<arr2.length;j++)
  {
   if(arr1[i]==arr2[j])
   diff=0;
  
  }
  if(diff)
  diffarr[diffarr.length]=arr1[i];
 }
 return diffarr;
}
//-------------
cloud.request.unlink =function()
{
 var requestobj =
 {
 actionname:'delete',
  directory:cloud.connection.cwd,
     fflist:cloud.selector()
 };

 if(requestobj.fflist.length && confirm("are you sure")==true)
 {
 var requeststr=JSON.stringify(requestobj);
 $.post(cloud.connection.requestto,{req:requeststr},function(data,status) {$('.info').html(data+status); cloud.request.dir(cloud.connection.cwd);});

 }
 }
//----------------
cloud.request.createfolder =function()
{
 var fname;
 if( fname=prompt("folder name:"))
 {
  var requestobj=
  {
  actionname:"createfolder",
   directory:cloud.connection.cwd,
  foldername:fname
  };
 if(array_common([fname],cloud.dirread()).length)
 {alert("can't create folder preexist");return}
  
 var requeststr=JSON.stringify(requestobj);
 
 $.post(cloud.connection.requestto,{req:requeststr},function(data,status) {$('.info').html(data+status);cloud.request.dir(cloud.connection.cwd);});

	
 }
}
//-----------------
cloud.request.ffdetail=function ()
{
 var requestobj =
 {
 actionname:'ffdetail',
  directory:cloud.connection.cwd,
     ffname:cloud.selector()[0]
 };

 if(requestobj.ffname)
 {
 var requeststr=JSON.stringify(requestobj);
 
 $.post(cloud.connection.requestto,{req:requeststr},function(data,status) {$('.info').html(data+status);});
 }
}

//-----unzip
cloud.request.unzip=function ()
{
 var requestobj =
 {
 actionname:'unzip',
  directory:cloud.connection.cwd,
       name:cloud.selector()[0]
 };

 if(requestobj.name)
 {
 var requeststr=JSON.stringify(requestobj);
 $.post(cloud.connection.requestto,{req:requeststr},function(data,status) {$('.info').html(data+status); cloud.request.dir(cloud.connection.cwd);});

 }
}

//-----
cloud.request.rename=function()
{
 var Name='';	
 if(!cloud.selector().length)return;
 if(!(Name=prompt("newname for "+cloud.selector()[0])))return;
 alert(2);
 var requestobj =
 {
 actionname:'rename',
  directory:cloud.connection.cwd,
     name:cloud.selector()[0],
  newname:Name
 };

 if(requestobj.name)
 {
 var requeststr=JSON.stringify(requestobj);
 
 $.post(cloud.connection.requestto,{req:requeststr},function(data,status) {$('.info').html(data+status);cloud.request.dir(cloud.connection.cwd);});
 }

}
//---
cloud.request.search=function()
{
 var inputsearch=$("#search").val();
 
 
  var requestobj =
 {
 actionname:'search',
  directory:cloud.connection.cwd,
     search:inputsearch
 };

 if(requestobj.search)
 {
 var requeststr=JSON.stringify(requestobj);
 
 $.post(cloud.connection.requestto,{req:requeststr},function(data,status) {$('.info').html(data+status);});
 }
cloud.request.dir(cloud.connection.cwd);
}
//--------------------
cloud.request.download=function()
{
 var requestobj =
 {
 actionname:'download',
  directory:cloud.connection.cwd,
     fflist:cloud.selector()
 };
 
 if(requestobj.fflist.length)
 {
 var requeststr=JSON.stringify(requestobj);
 
/*
$.post(cloud.connection.requestto,{req:requeststr},function(data,status){if(status=="success"){blob=new Blob([data],{type:'application/zip'});var downloadUrl = URL.createObjectURL(blob);$('.info').html(blob.size+':'+data.length+':');if(blob.size)window.location=downloadUrl;}});*/
window.open(cloud.connection.requestto+'&req='+requeststr);
 }
 else
 alert("no one selected");
}
//--------------------------------------------------
//                   
//--------------------------------------------------
cloud.request.upload=function()
{
if(!$("#inputfile")[0].files.length)return;
var uploadfiles=$("#inputfile")[0].files;
mydata=new FormData();
for(j=0;j<uploadfiles.length;j++)
mydata.append("files["+j+"]",uploadfiles[j],uploadfiles[j].name);

$("#uploadbtn").attr('disabled','true');$("#inputfile").attr('disabled','true');
$(".impinfo").html('uploading..');

var requeststr=JSON.stringify({actionname:'upload', directory:cloud.connection.cwd});
$.ajax({url:cloud.connection.requestto,data:mydata,headers:{Etag:requeststr,"Content-Range":"bytes "+(0)+"-"+1+"/"+10},type:"POST",processData:!1,contentType:false}).always(function(){;}).done(function(a,d,h){$("#inputfile").val('');
$(".impinfo").html(a);$("#uploadbtn").attr('disabled',!1);$("#inputfile").attr('disabled',!1);}).fail(function(){alert("uoloading fail!");$(".impinfo").html(a);$("#uploadbtn").attr('disabled',!1);$("#inputfile").attr('disabled',!1);});
cloud.request.dir(cloud.connection.cwd);
}

//--------------------------------------------------
//                   display
//--------------------------------------------------
cloud.display={};
cloud.display.dir=function(data)
{
 if(data.length>0)
 {
 $('#directory').html('');
 for(var i=0;i<data.length;i++)
 $('#directory').append(" <div class='dir-item' name=\""+data[i].name+"\"> <input type='checkbox'/><span style='padding:0px 2px;'>"+data[i].name+"</span> </div>");

 }
 else
 $('#directory').html('empty');
 
 $('#directorypath').html ('');
 $('#directorypath').append("<span name='"+cloud.connection.root+"'>root</span> &#187; ");
  
  var req=cloud.connection.cwd;
  var indexcontrol=0;
  for(i=0;i<req.length;i++)
  {
   if(req.charAt(i)=='/' && indexcontrol!=0)
    {
     var  nam=req.slice(indexcontrol+1,i);
     var conn=req.slice(0,i);
     
     $('#directorypath').append("<span name=\""+conn+"\">"+nam+"</span> &#187;");
    
    }
    else if(req.length-1==i && indexcontrol!=0)
    {
    var  nam=req.slice(indexcontrol+1,i+1);
     var conn=req.slice(0,i+1);

    $('#directorypath').append("<span name='"+conn+"'>"+nam+"</span> ");
    }
    if(req.charAt(i)=='/') indexcontrol=i;
  }
  

 
}



//--------------------------------------------------
//                   eventlistner
//--------------------------------------------------
$(document).on('click','.dir-item span',function (){cloud.request.dir(cloud.connection.cwd+'/'+$(this).closest('div').attr('name'));});
$(document).on('click','#directorypath span',function (){cloud.request.dir($(this).attr('name'));});

$(document).on('click','#unlink',cloud.request.unlink);

$(document).on('click','#cut',cloud.request.cut);

$(document).on('click','#copy',cloud.request.copy);

$(document).on('click','#paste',cloud.request.paste);

$(document).on('click','#createfolder',cloud.request.createfolder);
$(document).on('click','#detail',cloud.request.ffdetail);
$(document).on('click','#searchbtn',cloud.request.search);
$(document).on('click','#download',cloud.request.download);
$(document).on("click","#ffrename",cloud.request.rename);
$(document).on('click','#uploadbtn',cloud.request.upload);
$(document).on('click','#unzip',cloud.request.unzip);
 
 </script> 
</html> 











