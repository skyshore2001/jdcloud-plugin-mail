function api_sendMail()
{
	$type = mparam("type");
	$id = mparam("id");
	if ($type === "test") {
		checkAuth(PERM_TEST_MODE);
		JDMailer::sendMail("liangjian@oliveche.com", '缺陷[1595]-新建待判定', '<a href="http://localhost/p/pdi/web/store.html#pageIssue">缺陷[1595]</a>-新建待判定');
	}
	if ($type === "Issue") {
		// $row = callSvcInt("Issue.get", ["id"=>$id, "res"=>"tm,status,name"]);
		$row = queryOne("SELECT tm,status,name FROM Issue WHERE id=" . $id, true);
		if ($row === false)
			throw new MyException(E_PARAM, "bad $type id $id");

		$IssueStatusMap = [
			"CR" => "新建(待判定)",
			"TF" => "待维修",
			"FX" => "返修完成(待确认)",
			"CL" => "关闭"
		];
		$status = $IssueStatusMap[$row["status"]] ?: $row["status"];
		$subject = "缺陷[$id] - $status - " . $row["name"];
		$url = getBaseUrl(true) . "web/store.html#pageIssue";
		$content = AccessControl::table2html([
			"h" => ["缺陷编号", "创建时间", "状态", "标题"],
			"d" => [
				["<a href=\"$url\">$id</a>", $row["tm"], $status, $row["name"]]
			]
		], true);

		$to = queryOne("SELECT group_concat(email) FROM Employee WHERE find_in_set('qmgr', perms)");
		// return [$to, $subject, $content];
		JDMailer::sendMail($to, $subject, $content);
	}
}
