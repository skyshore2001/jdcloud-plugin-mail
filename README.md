# 邮件通知

创建某类对象后，为管理员发送邮件通知。

## 设计

示例应用：在车辆出厂检测时，若发现并创建了缺陷问题，则邮件通知质量管理员。

后端接口设计：

	sendMail(type, id)

- type: 一般就用表的名字。比如`Issue`表示缺陷。type="test"时用于测试。
- id: 关联的对象编号。与type合在一起就能定位到一个具体的对象了。

内部接口：

	JDMailer::sendMail($to, $subject, $content)

测试发送：

	curl -i "http://localhost/pdi/api.php/sendMail?type=test&id=1"	

可检查日志trace.log。

若要详细调试信息，可在conf.user.php中开启：

	$g_smtp_debug = 1;

## 用法

### 安装插件jdcloud-plugin-mail

使用git clone下载插件后，假定插件路径与jdcloud项目路径相同。进入jdcloud项目下，打开git-bash运行命令安装插件：

	./tool/jdcloud-plugin.sh add ../jdcloud-plugin-mail

若要删除插件可以用

	./tool/jdcloud-plugin.sh del jdcloud-plugin-mail

添加或更新的文件将自动添加到git库中，插件安装信息保存在文件plugin.dat中。

### 后端实现

后端去修改`api_functions.php`文件中的`api_sendMail`接口，实现业务逻辑（其中包含快速测试接口）。

系统配置：将conf.user.template.php中的示例配置项，复制到conf.user.php中并修改即可。

在测试模式下，调用接口进行测试：

	GET $BASE_URL/api.php/sendMail?type=test&id=1

在创建Issue时发送邮件，可以用：

	class AC2_Issue extends AccessControl
	{
		protected function onValidate() {
			if ($this->ac === "add") {
				$this->onAfterActions[] = function () {
					callSvcAsync("sendMail", ["type"=>"Issue", "id"=>$this->id]);
				};
			}
		}
	}

callSvcAsync是框架提供的函数，用于在当前事务完成后，发起一个调用并立即返回（不等服务端输出数据）。

### 前端实现

（无）
