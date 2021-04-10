  这套roles只针对centos7 系统

  ##安装mysql
  ansible-playbook mysql.yml --extra-vars "host=test  installation_type=yum mysql_root_password=qasx16mnb42*gp316hcxDje"
  host:要安装的服务器名称 
  installation_type:[yum,source] yum表示使用yum安装 source表示编译安装 目前就yum
  mysql_root_password: 初始化的时候root 账号的密码 


  ##mysql 添加用户
  ansible-playbook mysql_add_user.yml --extra-vars "host=test login_password=qasx16mnb42*gp316hcxDje user_name=l3n641 password=qasx16mnb42*gp316hcxDje_1"
  host:要安装的服务器名称 
  login_password: root 密码
  user_name: 要注册的账号
  password: 新账号的密码

  ##安装php 服务
  ansible-playbook php.yml --extra-vars "host=test"
  host:要安装的服务器名称 

  ##安装nginx服务
  ansible-playbook nginx.yml --extra-vars "host=test"
  host:要安装的服务器名称 

  ##安装python
  ansible-playbook python.yml --extra-vars "host=test"
  host:要安装的服务器名称 

  ##安装mongodb
  ansible-playbook mongo.yml --extra-vars "host=test python3_interpreter_path=/usr/local/python3/bin/python3"
  host:要安装的服务器名称

  ##安装opencart_cms
  ansible-playbook install_cms.yml --extra-vars "host=test cms_type=opencart domain=test4.fxteam.top  python3_interpreter_path=/usr/local/python3/bin/python3 database_user=root database_password=qasx16mnb42*gp316hcxDje database_name=test4_opencart  database_port=3306 opencart_admin_account=admin opencart_admin_password=admin opencart_admin_email=111@qq.com  opencart_db_prefix=test_ db_driver=mpdo"

  ##安装opencart_cms 上传商品插件
  ansible-playbook plugin_opencart_upload_product.yml --extra-vars "host=test domain=test5.fxteam.top"
