  这套roles只针对centos7 系统

  ##安装mysql
  ansible-playbook mysql.yml --extra-vars "host=test  installation_type=yum mysql_root_password=qasx16mnb42*gp316hcxDje"
  host:要安装的服务器名称 
  installation_type:[yum,source] yum表示使用yum安装 source表示编译安装 目前就yum
  mysql_root_password: 初始化的时候root 账号的密码 


  ##mysql 添加数据库和对应的用户
  ansible-playbook mysql_add_database.yml --extra-vars "host=test database_name=test123 db_login_user=root  db_login_password=qasx16mnb42*gp316hcxDje  db_login_host=127.0.0.1  user_name=admin_4 password=qasx16mnb42*gp316hcxDje_1 python3_path=/venv/bin/python3  host:要安装的服务器名称
  database_name: 要创建的数据库名称
  db_login_user: 管理员账号
  db_login_password: 管理员密码
  db_login_host: 127.0.0.1
  user_name: 要注册的账号
  password: 新账号的密码
  python3_path: 装有pymysql的python路径

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
  ansible-playbook install_cms.yml --extra-vars "host=test cms_type=opencart domain=test9.fxteam.top  python3_interpreter_path=/usr/local/python3/bin/python3 database_user=opencart_zhihu database_password=china999 database_name=test9_opencart  database_port=3306 opencart_admin_account=admin opencart_admin_password=admin opencart_admin_email=111@qq.com  opencart_db_prefix=test_ db_driver=mpdo db_login_user=root db_login_password=qasx16mnb42*gp316hcxDje"
  ##安装opencart_cms 上传商品插件
  ansible-playbook plugin_opencart_upload_product.yml --extra-vars "host=test domain=test5.fxteam.top"
