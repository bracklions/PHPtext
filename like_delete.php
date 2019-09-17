<?php
// ◎◎◎◎◎◎◎◎◎　追加機能　◎◎◎◎◎◎◎◎◎◎ START ※like_insertのファイル自体新規追加
session_start();
require('dbconnect.php');

error_log(print_r($_SESSION,true),"3","log/debug(like_delete1).log");

if (isset($_SESSION['id'])) {

  error_log(print_r($_REQUEST,true),"3","log/debug(like_delete2).log");
  // いいねが登録済みか検査する
    // GROUP BY でこのままだと0件の時にNULLになる
    // $likes = $db->prepare('SELECT COUNT(liked_post_id) AS like_cnt FROM likes WHERE liked_post_id=? AND pressed_member_id=? GROUP BY liked_post_id');
  // SQL文を副問い合わせという形にする
  $likes = $db->prepare('SELECT COUNT(liked_post_id) AS like_cnt FROM (SELECT liked_post_id FROM likes WHERE liked_post_id=? AND pressed_member_id=? GROUP BY liked_post_id) A');
  $likes->execute(array(
    $_REQUEST['id'], // liked_post_id いいねを取消しようとしているメッセージのid
    $_SESSION['id']  // pressed_member_id いいねを取消しようとしているメンバーのid
  ));
  $like = $likes->fetch();
  error_log(print_r($like,true),"3","log/debug(like_delete3).log");

  // いいねを取消しようとしている投稿が1件のみか確認する
  if ($like['like_cnt'] == 1) {
    // いいねを削除する
    $like_ins = $db->prepare('DELETE FROM likes WHERE liked_post_id=? AND pressed_member_id=?');
    $like_ins->execute(array(
      $_REQUEST['id'], // liked_post_id いいねしようとしているメッセージのid
      $_SESSION['id']  // pressed_member_id いいねしようとしているメンバーのid
    ));
  }
}

header('Location: index.php');
exit();
?>