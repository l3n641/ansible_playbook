ANSIBLE_METADATA = {
    'metadata_version': '1.1',
    'status': ['preview'],
    'supported_by': 'community'
}

DOCUMENTATION = '''
---
module: opencart_install

short_description: This is install opencart  cms

version_added: "1.0"

description:
    - "安装opencart cms"

author:
    - l3n641 (hh250@qq.com)
'''

EXAMPLES = '''
 ansible centos7_3 -m opencart_install -a "database_user=root database_password=qasx16mnb42*gp316hcxDje database_name=test_1  database_port=3306 opencart_admin_account=admin opencart_admin_password=admin opencart_admin_email=111@qq.com  domain=www.china.com dir_opencart=/datas/www/www.china.com opencart_db_prefix=test_ " -e 'ansible_python_interpreter=/venv/bin/python3 '
'''

from ansible.module_utils.basic import AnsibleModule
import re, datetime, os, stat
import hashlib
import pymysql
from pymysql.err import OperationalError


class DabatabseNotExistError(AttributeError):
    pass


class MysqlDb():

    def __init__(self, host, user, password, db_name, port=3306, charset="utf8"):
        try:
            self.create_database(host=host, port=port, user=user, password=password, db_name=db_name, charset=charset)
            self.db = pymysql.Connect(host=host, port=port, user=user, passwd=password, db=db_name, charset=charset)
            self.cursor = self.db.cursor()

        except OperationalError as error:
            print(error)
        except AttributeError as error:
            print(error)

    def query(self, sql):
        self.cursor.execute(sql)
        return self.cursor.fetchall()

    def get_last_id(self):
        return self.cursor.lastrowid

    def close(self):
        try:
            self.db.close()
        except AttributeError as error:
            print("没有找到对应的数据库")

    def create_database(self, host, user, password, db_name, port=3306, charset="utf8"):

        conn = pymysql.connect(host=host, user=user, password=password, port=port, charset=charset)
        cursor = conn.cursor()
        # 创建数据库的sql(如果数据库存在就不创建，防止异常)
        sql = "CREATE DATABASE IF NOT EXISTS {}".format(db_name)
        cursor.execute(sql)


class InitDatabase():

    def __init__(self, db: MysqlDb, sql_file):
        self.db = db
        self.sql_file = sql_file

    def _token(self, len):
        import random
        seed = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"
        sa = []
        for i in range(len):
            sa.append(random.choice(seed))
        salt = ''.join(sa)
        return salt

    def execute_sql_file(self, sql_file_path, db_prefix=""):

        with open(sql_file_path) as text_file:
            lines = text_file.readlines()
            sql = ""
            sql_list = []
            for line in lines:
                if line.split() and line[0:2] != "--" and line[0:1] != "#":
                    sql = sql + line
                    if re.search(";\s*$", sql):
                        sql = sql.replace("DROP TABLE IF EXISTS `oc_", "DROP TABLE IF EXISTS `" + db_prefix)
                        sql = sql.replace("CREATE TABLE `oc_", "CREATE TABLE `" + db_prefix)
                        sql = sql.replace("INSERT INTO `oc_", "INSERT INTO `" + db_prefix)
                        sql_list.append(sql)
                        self.db.query(sql)
                        sql = ""

    def run(self, username, password, email, db_prefix=""):
        self.execute_sql_file(sql_file_path=self.sql_file, db_prefix=db_prefix)
        self.pre_update()
        self.update_admin(username, password, email, db_prefix=db_prefix)
        self.update_setting(email, db_prefix=db_prefix)
        self.update_product(db_prefix)
        self.update_api(db_prefix)

    def pre_update(self):
        self.db.query("SET CHARACTER SET utf8")

    def update_admin(self, username, password, email, db_prefix=""):
        token = self._token(9)
        password = self._encryption(password, token)
        self.db.query("DELETE FROM `{db_prefix}user` WHERE user_id = '1'".format(db_prefix=db_prefix));
        sql = "INSERT INTO `{db_prefix}user` SET user_id = '1', user_group_id = '1', username = '{username}',salt = '{token}',password='{password}',firstname = 'John'," \
              " lastname = 'Doe', email ='{email}', status = '1', date_added = NOW()".format(username=username,
                                                                                             password=password,
                                                                                             token=token,
                                                                                             email=email,
                                                                                             db_prefix=db_prefix)
        print(sql)
        self.db.query(sql)

    def _encryption(self, password, salt):
        # sha1($salt . sha1($salt . sha1($data['password']))))
        sha1 = hashlib.sha1()
        sha1.update(password.encode('utf-8'))
        password = sha1.hexdigest()

        sha1 = hashlib.sha1()
        sha1.update((salt + password).encode('utf-8'))
        password = sha1.hexdigest()

        sha1 = hashlib.sha1()
        sha1.update((salt + password).encode('utf-8'))
        return sha1.hexdigest()

    def update_setting(self, email, db_prefix=''):
        self.db.query("DELETE FROM `{db_prefix}setting` WHERE `key` ='config_email'".format(db_prefix=db_prefix))
        sql = "INSERT INTO `{db_prefix}setting` SET `code` = 'config', `key` = 'config_email', value = '{email}'".format(
            email=email, db_prefix=db_prefix)

        self.db.query(sql)

        token = self._token(1024)
        self.db.query("DELETE FROM `{db_prefix}setting` WHERE `key` = 'config_encryption'".format(db_prefix=db_prefix));
        sql2 = "INSERT INTO `{db_prefix}setting` SET `code` = 'config', `key` = 'config_encryption', value = '{token}'".format(
            token=token, db_prefix=db_prefix)

        self.db.query(sql2)

        # set the current years prefix
        year = datetime.datetime.now().year
        sql3 = "UPDATE `{db_prefix}setting` SET `value` = 'INV-{year}-00' WHERE `key` = 'config_invoice_prefix'".format(
            year=year, db_prefix=db_prefix)
        self.db.query(sql3)

    def update_product(self, db_prefix=''):
        self.db.query("UPDATE `{db_prefix}product` SET `viewed` = '0'".format(db_prefix=db_prefix));

    def update_api(self, db_prefix):
        token = self._token(256)
        sql = "INSERT INTO `{db_prefix}api` SET username = 'Default', `key` = '{token}', status = 1," \
              " date_added = NOW(), date_modified = NOW()".format(token=token, db_prefix=db_prefix)

        self.db.query(sql)
        api_id = self.db.get_last_id()
        self.db.query("DELETE FROM `{db_prefix}setting` WHERE `key` = 'config_api_id'".format(db_prefix=db_prefix));
        sql2 = "INSERT INTO `{db_prefix}setting` SET `code` = 'config', `key` = 'config_api_id', value = '{api_id}'".format(
            api_id=api_id, db_prefix=db_prefix)

        self.db.query(sql2)


class InitConfigFile():

    def __init__(self, domain, dir_opencart, db_driver, db_host, db_username, db_password, db_database, db_port=3306,
                 db_prefix=''):
        # config.php 常量定义
        self.DIR_OPENCART = dir_opencart
        self.HTTP_OPENCART = domain
        self.DIR_APPLICATION = os.path.join(dir_opencart, "catalog/")
        self.DIR_SYSTEM = os.path.join(dir_opencart, "system/")
        self.DIR_IMAGE = os.path.join(dir_opencart, "image/")

        # 数据库配置
        self.db_driver = db_driver
        self.db_host = db_host
        self.db_username = db_username
        self.db_password = db_password
        self.db_database = db_database
        self.db_port = db_port
        self.db_prefix = db_prefix

    def _get_front_config_template(self):
        str = """
        <?php
        // HTTP
         define('HTTP_SERVER', 'http://{domain}/');

        // HTTPS
         define('HTTPS_SERVER', 'http://{domain}/');

        // DIR
         define('DIR_APPLICATION', '{dir_application}');
         define('DIR_SYSTEM', '{dir_system}');
         define('DIR_IMAGE', '{dir_image}');
         define('DIR_STORAGE', DIR_SYSTEM . 'storage/');
         define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
         define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
         define('DIR_CONFIG', DIR_SYSTEM . 'config/');
         define('DIR_CACHE', DIR_STORAGE . 'cache/');
         define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
         define('DIR_LOGS', DIR_STORAGE . 'logs/');
         define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
         define('DIR_SESSION', DIR_STORAGE . 'session/');
         define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

        // DB
         define('DB_DRIVER', '{db_driver}');
         define('DB_HOSTNAME', '{db_host}');
         define('DB_USERNAME', '{db_username}');
         define('DB_PASSWORD', '{db_password}');
         define('DB_DATABASE', '{db_database}');
         define('DB_PORT', '{db_port}');
         define('DB_PREFIX', '{db_prefix}');
            """

        return str

    def _get_backend_config_template(self):
        str = """
        <?php
        // HTTP
         define('HTTP_SERVER', 'http://{domain}/admin/');
         define('HTTP_CATALOG', 'http://{domain}/');

        // HTTPS
         define('HTTPS_SERVER', 'http://{domain}/admin/');
         define('HTTPS_CATALOG', 'http://{domain}/');

        // DIR
         define('DIR_APPLICATION', '{dir_application}');
         define('DIR_SYSTEM', '{dir_system}');
         define('DIR_IMAGE', '{dir_image}');
         define('DIR_STORAGE', DIR_SYSTEM . 'storage/');
         define('DIR_CATALOG', '{dir_catalog}');
         define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
         define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
         define('DIR_CONFIG', DIR_SYSTEM . 'config/');
         define('DIR_CACHE', DIR_STORAGE . 'cache/');
         define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
         define('DIR_LOGS', DIR_STORAGE . 'logs/');
         define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
         define('DIR_SESSION', DIR_STORAGE . 'session/');
         define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

        // DB
         define('DB_DRIVER', '{db_driver}');
         define('DB_HOSTNAME', '{db_host}');
         define('DB_USERNAME', '{db_username}');
         define('DB_PASSWORD', '{db_password}');
         define('DB_DATABASE', '{db_database}');
         define('DB_PORT', '{db_port}');
         define('DB_PREFIX', '{db_prefix}');

        // OpenCart API
         define('OPENCART_SERVER', 'https://www.opencart.com/');
        """
        return str

    def write_front_config(self, file_path):
        config_template = self._get_front_config_template()
        config = config_template.format(domain=self.HTTP_OPENCART,
                                        dir_application=os.path.join(self.DIR_OPENCART, "catalog/"),
                                        dir_system=self.DIR_SYSTEM, dir_image=self.DIR_IMAGE, db_driver=self.db_driver,
                                        db_host=self.db_host, db_username=self.db_username,
                                        db_password=self.db_password,
                                        db_database=self.db_database, db_port=self.db_port, db_prefix=self.db_prefix)

        file_path = os.path.join(file_path, 'config.php')
        with open(file_path, 'w') as file:
            file.write(config)

    def write_backend_config(self, file_path):
        config_template = self._get_backend_config_template()
        config = config_template.format(domain=self.HTTP_OPENCART,
                                        dir_application=os.path.join(self.DIR_OPENCART, "admin/"),
                                        dir_system=self.DIR_SYSTEM, dir_image=self.DIR_IMAGE, db_driver=self.db_driver,
                                        db_host=self.db_host, db_username=self.db_username,
                                        db_password=self.db_password,
                                        db_database=self.db_database, db_port=self.db_port, db_prefix=self.db_prefix,
                                        dir_catalog=os.path.join(self.DIR_OPENCART, "catalog/"), )

        file_path = os.path.join(file_path, 'admin')
        if not os.path.exists(file_path):
            os.mkdir(file_path)
        file_path = os.path.join(file_path, 'config.php')
        with open(file_path, 'w') as file:
            file.write(config)


def change_dir_permision(dir_opencart):
    dirs = [
        'image/',
        'system/storage/download/',
        'system/storage/upload/',
        'system/storage/cache/',
        'system/storage/logs/',
        'system/storage/modification/', ]

    for dir in dirs:
        dir = os.path.join(dir_opencart, dir)
        os.chmod(dir, stat.S_IRWXU | stat.S_IRWXG | stat.S_IRWXO)


def run_module():

    module_args = dict(
        # 数据库设置
        database_host_addr=dict(type='str', required=False, default="127.0.0.1"),
        database_user=dict(type='str', required=False, default="root"),
        database_password=dict(type='str', required=True),
        database_name=dict(type='str', required=True),
        database_port=dict(type=int, required=True),

        # 安装cms 的sql 文件
        sql_file_path=dict(type='str', required=False, default="/datas/resource/opencart/opencart.sql"),

        # opencart cms 设置
        opencart_admin_account=dict(type='str', required=True),
        opencart_admin_password=dict(type='str', required=True),
        opencart_admin_email=dict(type='str', required=True),
        opencart_db_prefix=dict(type='str', required=False, default=""),
        domain=dict(type='str', required=True, ),
        dir_opencart=dict(type='str', required=True, ),  # opencart 安装目录
        db_driver=dict(type='str', required=False, default="mysqli"),

    )

    result = dict(
        changed=False,
        message=''
    )

    module = AnsibleModule(
        argument_spec=module_args,
        supports_check_mode=True
    )

    db = MysqlDb(module.params['database_host_addr'], module.params['database_user'],
                 module.params['database_password'], module.params['database_name'], module.params['database_port'])

    init_db = InitDatabase(db, module.params['sql_file_path'])

    init_db.run(module.params['opencart_admin_account'], module.params['opencart_admin_password'],
                module.params['opencart_admin_email'], module.params['opencart_db_prefix'])

    db.close()

    init_config = InitConfigFile(module.params['domain'], module.params['dir_opencart'], module.params['db_driver'],
                                 module.params['database_host_addr'], module.params['database_user'],
                                 module.params['database_password'], module.params['database_name'],
                                 module.params['database_port'], module.params['opencart_db_prefix']
                                 )
    init_config.write_front_config(module.params['dir_opencart'])
    init_config.write_backend_config(module.params['dir_opencart'])

    change_dir_permision(module.params['dir_opencart'])

    result['message'] = "安装成功"
    module.exit_json(**result)


def main():
    run_module()


if __name__ == '__main__':
    main()
