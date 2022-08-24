<?php
switch($action)
{
	case 'add':
		$smarty->display('admin_changelog_add.html');
		break;
	
	case 'addsave':
		$title = $_POST['title'];
		$content = $_POST['content'];
		$now = time();
		if($title=='' || $content=='')
		{
			echo '请填写标题和内容！';
		}
		else
		{
			$db->query("INSERT INTO game_changelogs(title,content,dateline) VALUES ('$title','$content',$now)");
			echo '<a href="admin.php?module=changelog">更新日志添加成功，点击返回列表</a>';
		}
		break;
		
	case 'edit':
		$id = $_GET['id'];
		$changeLogInfo = $db->fetch_first("SELECT * FROM game_changelogs WHERE id=$id");
		$smarty->assign('id', $id);
		$smarty->assign('title', $changeLogInfo['title']);
		$smarty->assign('content', $changeLogInfo['content']);
		$smarty->display('admin_changelog_edit.html');
		break;
	
	case 'editsave':
		$id = $_GET['id'];
		$title = $_POST['title'];
		$content = $_POST['content'];
		if($id=='' || $title=='' || $content=='')
		{
			echo '请填写标题和内容！';
		}
		else
		{
			$db->query("UPDATE game_changelogs SET title='$title',content='$content' WHERE id=$id");
			echo '<a href="admin.php?module=changelog">更新日志编辑成功，点击返回列表</a>';
		}
		break;
	
	case 'remove':
		$id = $_GET['id'];
		$sql = "DELETE FROM game_changelogs WHERE id=$id";
		$db->query($sql);		
		echo '<a href="admin.php?module=changelog">删除成功，点击返回</a>';
		break;
	
	default:
		$changeLogQuery = $db->query("SELECT * FROM game_changelogs ORDER BY id DESC");
		while($changeLog = $db->fetch_array($changeLogQuery))
		{
			$changeLogList[] = array(
								   'id' => $changeLog['id'],
								   'title' => $changeLog['title'],
								   'content' => $changeLog['content'],
								   'dateline' => $changeLog['dateline'],
								   'hits' => $changeLog['hits']
								   );
		}
		$smarty->assign('changeLogList', $changeLogList);
		$smarty->display('admin_changelog.html');
}
