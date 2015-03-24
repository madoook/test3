<?php
echo "<meta charset='utf-8' />";
$db = mysql_connect('localhost', 'root','pass');
if (!$db){
die ("Не возможно подключится к БД<br />".mysql_error());
}
mysql_select_db('test',$db);
$query = mysql_query("SELECT id,author,date,text FROM post",$db);

while ($row = mysql_fetch_array($query)){
echo "Пост: <br>";
$id = $row['id'];
$autor = $row['author'];
$date = date("Y-m-d H:i:s", $row['date']);
$text = $row['text'];

echo "$autor </br>\n $date </br>\n $text</br>\n";

echo "Новый комент:</br>\n";
    echo <<<END
        <form action="$_SERVER[PHP_SELF]" method="post">
        Автор:
        <input type="text" name="author"></br>\n
		<input type="hidden" name="date" value=".$date."></br>\n
		<input type="hidden" name="pid" value="$id"></br>\n
		Текст:
		<textarea name="text" id="text" cols="20" rows="2"></textarea>
        <input type="hidden" name="stage" value="process">
        <input type="submit" value="Отправить">
        </form>
END;





$result=mysql_query("SELECT * FROM  comment WHERE `pid` = $id ");

if   (mysql_num_rows($result) > 0){
    $c_trees = array();
    while($c_tree =  mysql_fetch_assoc($result)){
        $c_trees_ID[$c_tree['id']][] = $c_tree;
        $c_trees[$c_tree['parent_id']][$c_tree['id']] =  $c_tree;
    }
}
if ((mysql_num_rows($result) > 0)){
echo build_tree($c_trees,0);}
}
function build_tree($c_trees,$parent_id){
    if(is_array($c_trees) and isset($c_trees[$parent_id])){
        $tree = '<ul>';
        
            foreach($c_trees[$parent_id] as $c_tree){
				$id = $c_tree['id'];
				$pid = $c_tree['pid'];
                $tree .= '<li>'.  $c_tree['author'].' <br>'.$c_tree['text'].' <br>'.date("Y-m-d H:i:s", $c_tree['date']);
				$tree .= "<form action='$_SERVER[PHP_SELF]' method='post'>\n";
				$tree .= "<input  type='hidden'  name='cpid'  value='$pid'/>\n";
				$tree .= "<input  type='hidden'  name='ccid'  value='$id'/>\n";
				$tree .= "Автор: <input type='text' name='c_author'></br>\n";
				$tree .= "Текст: <textarea name='c_text' id='c_text' cols='10' rows='1'></textarea></br></br>\n";
				$tree .= "<input type='submit' value='Ответ'>\n";
				$tree .= "</form>\n";
                $tree .=  build_tree($c_trees,$c_tree['id']);				
                $tree .= '</li>';        
				
				}
       
        $tree .= '</ul>';
		
    }
    else return null;
    return $tree;
}
	
	if (isset($_POST['stage'])) {$stage = $_POST['stage'];}
	
		if ($stage == ''){
	if (isset($_POST['cpid'])) {$pcid= $_POST['cpid']; if ($pcid == '') {unset ($pcid);}} 
	if (isset($_POST['ccid'])) {$ccid= $_POST['ccid']; if ($ccid == '') {unset ($ccid);}} 
	if (isset($_POST['c_author'])) {$c_author= $_POST['c_author']; if ($c_author == '') {unset ($c_author);}} 	
	if (isset($_POST['c_text'])) {$c_text= $_POST['c_text']; if ($c_text == '') {unset ($c_text);}} 
	
		if (isset($ccid) && isset($c_author)&& isset($_POST['c_author'])){ 
		$date = time();
		$result = mysql_query ("INSERT INTO comment (`id`, `pid`, `parent_id`, `date`, `author`, `text`) VALUES ('','$pcid','$ccid', '$date', '$c_author','$c_text')");
		
        header("Location: ".$_SERVER['PHP_SELF']);


		}

	}
		else
	
	{
	if (isset($_POST['pid'])) {$pid= $_POST['pid'];          if ($pid == '')    {unset ($pid);}} 
	if (isset($_POST['text'])) {$text= $_POST['text'];       if ($text == '')   {unset ($text);}} 
	if (isset($_POST['author'])) {$author= $_POST['author']; if ($author == '') {unset ($author);}}
		if (isset($pid) && isset($author) && isset($text)){
		$date = time();
		$result = mysql_query ("INSERT INTO comment (pid,date,author,text) VALUES ('$pid', '$date', '$author','$text')");

		header("Location: ".$_SERVER['PHP_SELF']);
		}
	
	}


?>