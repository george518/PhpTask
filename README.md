# 皮皮任务管理系统1.0

##使用场景
PHP执行定时任务或者常驻任务，在负载均衡（多台服务器）下，可以将PHP的任务按照设置分配到某一台或者某几台服务器执行任务。
注意：多台机器有一个共同的文件管理系统。用来存放uploads文件夹。

##原理

###1、将PHP任务写成shell文件，shell文件中控制执行几个进程
###2、需要执行任务的机器上安装执行器。安装的方法是将cron_服务器编号.sh放到crontab中。
###3、web中将需要执行的任务或者关闭的任务生成文件，执行器读取任务文件并执行相应的任务。


