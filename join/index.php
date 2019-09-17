<?php
session_start();

require('../dbconnect.php');
require('../htmlspecialchars.php');

if (!empty($_POST)) {
	// エラー項目の確認
	if ($_POST['name'] == '') {
		$error['name'] = 'blank';
	}
	if ($_POST['email'] == '') {
		$error['email'] = 'blank';
	}
	if (strlen($_POST['password']) < 4) {
		$error['password'] = 'length';
	}
	if ($_POST['password'] == '') {
		$error['password'] = 'blank';
	}
	$fileName = $_FILES['image']['name'];
	if (!empty($fileName)) {
		$ext = substr($fileName, -3);
		if ($ext != 'jpg' && $ext != 'gif' && $ext != 'JPG' && $ext != 'GIF') {
			$error['image'] = 'type';
		}
	}

	// 重複アカウントのチェック
	if (empty($error)) {
		$member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
		$member->execute(array($_POST['email']));
		$record = $member->fetch();
		if ($record['cnt'] > 0) {
			$error['email'] = 'duplicate';
		}
	}

	if (empty($error)) {
		// 画像をアップロードする $_FILES['image']の['name']
		$image = date('YmdHis').$_FILES['image']['name'];
		move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/'.$image);
		$_SESSION['join'] = $_POST;
		$_SESSION['join']['image'] = $image;
		header('Location: check.php');
		exit();
	}
}

// 書き直し
if (array_key_exists('acton', $_REQUEST) && $_REQUEST['action'] == 'rewrite') {
	$_POST = $_SESSION['join'];
	$error['rewrite'] = true;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="../../chapter06/post/style.css" />
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>会員登録</h1>
  </div>
  <div id="content">
		<p>次のフォームに必要事項をご記入ください。</p>
		<form action="" method="post" enctype="multipart/form-data">
		<dl>
			<dt>ニックネーム<span class="required">必須</span></dt>
			<dd>
				<input type="text" name="name" size="35" maxlength="255" value="<?php echo h(filter_input(INPUT_POST, 'name')); ?>" />
				<?php if (!empty($error) && array_key_exists('name', $error) && $error['name'] == 'blank'): ?>
					<p class="error">* ニックネームを入力してください</p>
				<?php endif; ?>
			</dd>

			<dt>メールアドレス<span class="required">必須</span></dt>
			<dd>
				<input type="text" name="email" size="35" maxlength="255" value="<?php echo h(filter_input(INPUT_POST, 'email')); ?>"/>
				<?php if (!empty($error) && array_key_exists('email', $error) && $error['name'] == 'blank'): ?>
					<p class="error">* メールアドレスを入力してください</p>
				<?php endif; ?>
			</dd>
			<dt>パスワード<span class="required">必須</span></dt>
			<dd>
				<input type="password" name="password" size="10" maxlength="20" value="<?php echo h(filter_input(INPUT_POST, 'password')); ?>"/>
				<?php if (!empty($error) && array_key_exists('password', $error) && $error['name'] == 'password'): ?>
					<p class="error">* パスワードを入力してください</p>
				<?php endif; ?>
				<?php if (!empty($error) && array_key_exists('password', $error) && $error['name'] == 'length'): ?>
					<p class="error">* パスワードは4文字以上で入力してください</p>
				<?php endif; ?>
			</dd>
			<dt>写真など</dt>
			<dd>
				<input type="file" name="image" size="35" />
				<?php if (!empty($error) && array_key_exists('image', $error) && $error['image'] == 'type'): ?>
					<p class="error">* gitとかにしてね</p>
				<?php endif; ?>
				<?php if (!empty($error)): ?>
					<p class="error">* もう一度画像を指定ください</p>
				<?php endif; ?>
			</dd>
		</dl>
		<div><input type="submit" value="入力内容を確認する" /></div>
		</form>
  </div>

</div>
</body>
</html>
