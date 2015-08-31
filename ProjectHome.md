自己编写的简洁可扩展MVC的php框架（出于安全考虑仅仅支持php 5.3.9及以上版本，文件编码仅仅支持utf8）

系统要求：适用于window、linux系统平台

编写初衷：原先想用thinkphp框架，后来经过阅读thinkphp代码，发现一个ip获取的欺骗bug以及非常严重的PDO封装还有漏洞，有可能导致注入的BUG，所以决定自己写框架系统，从最简单的方式开始，最基础架构先出来，后期会根据自己业务需求，封装扩展更多的类库及方法。

有关thinkphp的类库的bug，是3.2版本的bug，3.2.1官方根据我的提示，修复了bug，详见
http://www.thinkphp.cn/topic/10965.html 我和thinkphp官方讨论对话文章

框架宗旨：我一直觉得框架没有大小、好坏之分，适用才是关键，不同的场合可能不要不同的技术支持。编写这个框架主要是为了自己用，也同时为了跟大家一起分享学习。框架核心一点是安全，甚至可以因为安全性的要求牺牲一点框架性能。其次是根据需要的可扩展性，再其次是高效性，尽量减少不必要的类库加载。

---

2014-02-27 更新说明

---

1.增加上传类库Upload类，方便文件上传操作

2.增加分页类库Page类，方便分页操作


---

2014-02-25 更新说明

---

1.对session做了处理，支持memcache的扩展的服务器，默认session存储在memcache中提升效率

2.扩展图片操作类库，支持获取验证码、验证码校验（不区分大小写）、图片加水印（图片水印或者文字水印）、图片等比放大或缩小，均支持gif、png或jpg扩展名格式


---

2014-02-24 更新说明

---

更新对于所有的表单的post请求，增加了令牌token验证支持，可以用常量OPEN\_TOKEN设置是否开启令牌校验
HIDDEN\_TOKEN\_NAME常量设定令牌校验名称
action中使用$this->open\_token = true或者$this->open\_token = false设置针对性action方法是否开启令牌校验



---

2014-02-22 12:55:34 更新说明

---

更新由于疏忽if的模板中少了else if和else的问题，即增加了else if和else的模板的支持



---

2014-02-22 更新说明

---

1.修复url rewrite 没有添加action字符串


2.修复安全模式下server遇到array处理报错问题


3.在自制的模板引擎中，除了<{$val}> <{if ()}> <{/if}> <{loop()}> <{/loop}> <{foreach()}> <{/foreach}>之外增加include的支持，为代码模板公用提供了方便，提供两种实现方式，例子如
<{include="Index/test"}> 这里的action是Index,对应的action的方法method是test，而且模板也是test.html
<{include="Index/test/test\_view"}> 这里的action是Index,对应的action的方法method是test，而且模板是test\_view.html


---

2014-02-20 更新说明

---

1.修复了在关闭debug模式后，如果出现notice或者waring的情况的时候激发报错的提示问题

2.为了方便日后业务开发需求，增加Cache缓存类，支持文件缓存和memcache缓存，操作完全统一，通过get、set、rm等操作统一操作memcache或者文件缓存

【主要的功能介绍说明】

1.mysql操作方面使用pdo方式，摒弃以往的mysql\_connect或者mysqli\_connect，完全杜绝了SQL注入的可能性

2.简单快捷使用正则方式实现模板引擎，支持if、foreach、loop、直接赋值的模板操作

3.高效、快捷、可扩展，可以根据自身需要任意扩展类库，目前类库仅仅实现mysql的操作（PDO方式封装），可以根据需要扩展gd操作、文件操作、session操作、缓存等相关类库

4.全面支持MVC模式，类库、方法、函数自动加载机制

5.全面支持url rewrite的伪静态模式

6.出于安全考虑，封装了安全模式过滤函数，可以根据需要自己设定是否开启安全模式

7.2014-02-22更新的版本中增加了Cache缓存类，支持memcache和文件缓存两种模式，同时为了更好的代码复用，模板引擎在原有的基础上扩展了include公共模块的支持

8.支持post请求令牌token校验

9.扩展了一个图片操作扩展类库，支持图片验证码、图片缩小扩大、图片水印，添加session的memcache存储方式

温馨提示：如果大家需要下载csdn资源、pudn资源、51cto资源，可以到www.itziy.com/下载



---

2014-02-22 框架使用简单介绍

---

1.框架总体分为MVC的模式，下载后直接访问http://www.xxx.com/就可以自动生成相应的MVC架构。
当然是否生成demo是根据CREATE\_DEMO常量进行控制的,相关的其他常量请详见

framework/Framework.php里面的代码

2.支持控制过滤$_POST/$_GET/$_REQUEST/$_SERVER/$_COOKIE变量参数和值的安全模式_

3.framework/Common/functions.php定义了常用的函数，包括如下的一些，详见代码
getIp 获取ip地址
setc、getc、delc对于cookie的读、写、删操作
mkdirs、rm文件夹的创建和删除，支持批量创建和删除
send\_http\_status http状态头信息发送

C 读取配置文件
safe 安全模式过滤函数

build 生成框架例子的文件夹函数

echo\_memory\_usage 框架内存使用格式化输出函数

import 导入文件函数，主要用于记录框架引用的文件个数

debuginfo 调试信息输出函数

load\_functions 自动加载自定义的系统函数库文件以及用户自定义的函数库文件

my\_autoload 实现自动加载类库文件
U 对需要格式化的url进行格式化返回。格式如U(控制器名称/方法名称, array('参数key' => '参数value', '参数key2' => '参数value2')) example U('User/login', array('un' => 'zs', 'pw' => '123456'))

location 自动跳转函数，支持定义跳转的时间、消息内容，跳转地址

load\_tpl 自动加载模板函数

compress\_html 编译压缩html代码

shutdown\_function 捕获php异常处理函数（包括die、exit、或者致命错误导致程序终止的）

framework/Class/Action.php
简易实现了action操作公用的设置变量到模板的set方法和最终渲染模板的display方法，可以根据自行需要进行扩展，所有其他action都应该继承自这个action

framework/Class/Application.php
初始化框架、包括加载相关的函数库、建立框架所需的相应文件夹等，根据需要启动安全模式进行对$_POST/$_GET/$_REQUEST/$_SERVER/$_COOKIE变量参数和值的安全检查和过滤，解析url，利用web服务的url rewrite机制进行伪静态url处理，并且设置好相应的action、method、template并且调用action的相应方法，转交控制权_

framework/Class/Cache.php
实现了缓存控制、包括文件、内存缓存的统一方法、get、set、rm等操作

framework/Class/Demo.php
主要用于生成框架例子

framework/Class/Model.php
一个空的实现，您可以根据需要增加类似简化sql操作的方法实现，所有model继承此类

framework/Class/Mysql.php
利用单利模式构造的一个mysql操作类库，使用pdo方式封装彻底避免sql注入问题，其中关键是下面这个方法实现
query($sql, $data = array(), $one = false)
进行sql的值绑定方式，如果遇到insert自动返回最后插入的id值，如果是其他select操作根据需要返回一条或者全部记录数。

framework/Class/config.php
框架默认的配置文件，可以根究修改常量，重新自定义读取配置文件的位置

模板引擎说明
框架使用的模板引擎只是简单的一些正则替换、支持模板编译缓存，目前支持如下的一些模板引擎标记，包括if操作、直接赋值操作、foreach操作、loop操作、include操作，具体说明如下

直接赋值：通过在action方法中的set进行赋值到模板$this->set('val', 'hello world')，然后模板中使用<{$val}>

if 操作：
<{if (1 == 2)}>
test if
<{/if}>

foreach / loop 操作
<{foreach ($tarr as $t)}>
<{$t}>
<{/foreach}>
<{loop ($tarr as $t)}>
<{$t}>
<{/loop}>

include操作，支持两种方式，一直是template名称和method名称一样，另一种不一样，说明如下
<{include="Index/test"}> 这里的action是Index,对应的action的方法method是test，而且模板也是test.html
<{include="Index/test/test\_view"}> 这里的action是Index,对应的action的方法method是test，而且模板是test\_view.html


其他注意事项
1.如果使用mysql类库pdo会自动判断php版本

2.必须开启apache或者nginx的url rewrite模式
有其他使用问题，欢迎联系我，qq：563268276，共同学习探讨