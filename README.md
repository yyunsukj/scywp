# 个人云盘系统

基于PHP的轻量化个人云盘系统，支持阿里云OSS对象存储和MySQL数据库。

## 功能特性

- 用户认证和权限管理
- 文件/文件夹上传下载
- 批量文件操作
- 阿里云OSS对象存储
- MySQL数据库存储
- PC和移动端自适应
- 文件搜索和排序
- 分页显示

## 环境要求

- PHP 7.0 或更高版本
- MySQL 5.6 或更高版本
- 阿里云OSS账户
- 必须的PHP扩展：
  - mysqli
  - curl
  - xml
  - fileinfo

## 安装步骤

### 1. 克隆仓库

```bash
git clone https://github.com/yyunsukj/scywp.git
cd scywp
```

### 2. 安装依赖

```bash
composer install
```

### 3. 配置环境变量

复制 `.env.example` 文件为 `.env`：

```bash
cp .env.example .env
```

编辑 `.env` 文件，填入实际的配置信息：

```env
# 阿里云OSS配置
OSS_ACCESS_KEY_ID=your_aliyun_access_key_id
OSS_ACCESS_KEY_SECRET=your_aliyun_access_key_secret
OSS_BUCKET=scywp
OSS_ENDPOINT=http://oss-cn-shenzhen.aliyuncs.com

# MySQL数据库配置
DB_HOST=localhost
DB_USER=root
DB_PASS=your_database_password
DB_NAME=scyp
```

### 4. 创建数据库表

```sql
CREATE DATABASE IF NOT EXISTS scyp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE scyp;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT DEFAULT 0,
    file_type VARCHAR(50) DEFAULT 'file',
    upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 插入默认管理员账户
INSERT INTO users (username, password) VALUES ('admin', '$2y$10$B/6vo/TjPEO9dBRtncmoH.2sRMemIH1mS36/M7hG/Dd4.lbS4vT/q')
ON DUPLICATE KEY UPDATE username=username;
```

### 5. 启动开发服务器

```bash
php -S localhost:8000
```

访问 `http://localhost:8000` 即可使用系统。

## 默认账户

- 用户名: `admin`
- 密码: `admin`

## 项目结构

```
scywp/
├── config.php          # 配置文件
├── index.php           # 主文件
├── download.php        # 文件下载代理
├── login.php           # 登录页面
├── template.php        # 模板文件
├── composer.json       # Composer配置
├── .env.example        # 环境变量示例
├── file/               # 静态资源
├── uploads/            # 上传目录
└── vendor/             # Composer依赖
```

## 核心文件说明

### download.php
- 专门处理文件下载的代理服务
- 避免OSS Referer策略限制
- 支持单个文件下载和批量下载
- 服务器端代理，安全可靠

## 配置说明

### 阿里云OSS配置

- `OSS_ACCESS_KEY_ID`: 阿里云访问密钥ID
- `OSS_ACCESS_KEY_SECRET`: 阿里云访问密钥Secret
- `OSS_BUCKET`: OSS存储桶名称
- `OSS_ENDPOINT`: OSS访问地址

### MySQL数据库配置

- `DB_HOST`: 数据库主机地址
- `DB_USER`: 数据库用户名
- `DB_PASS`: 数据库密码
- `DB_NAME`: 数据库名称

## 安全建议

1. 不要将 `.env` 文件提交到版本控制系统
2. 定期更改默认管理员密码
3. 为OSS配置适当的访问权限
4. 启用HTTPS保护数据传输
5. 定期备份数据库

## 技术栈

- **后端**: PHP 8.2
- **数据库**: MySQL 8.0
- **对象存储**: 阿里云OSS
- **前端**: HTML5 + TailwindCSS + Vanilla JavaScript
- **依赖管理**: Composer

## 开发指南

### 添加新功能

1. 在 `index.php` 中添加后端逻辑
2. 在 `template.php` 中添加前端界面
3. 更新数据库表结构（如需要）
4. 测试功能正常工作

### 调试

启用PHP错误显示：

```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## 许可证

MIT License

## 联系方式

如有问题，请提交Issue或Pull Request。
