# Web File Uploader

一个基于 PHP 的 Web 文件管理器，界面设计参考了 iOS GCDWebUploader 的风格。本项目为局域网内使用的简单工具项目，不保证稳定性，请谨慎用于生产环境。


## 功能特性

- 文件上传/下载
- 文件夹创建/删除
- 文件重命名
- 文件移动
- 支持大文件上传（1GB）
- 支持拖拽上传
- 简洁的 iOS 风格界面设计

## 环境要求

- PHP 5.6+
- Nginx
- 现代浏览器（Chrome, Firefox, Safari, Edge）

## 目录结构

```
/project_root/
├── app/                # 应用程序目录
│   └── api/           # API接口
│       ├── create.php    # 创建文件夹
│       ├── delete.php    # 删除文件
│       ├── download.php  # 下载文件
│       ├── list.php      # 获取文件列表
│       ├── move.php      # 移动文件
│       └── upload.php    # 上传文件
├── web/            # Web根目录
│   ├── css/          # CSS文件
│   ├── js/           # JavaScript文件
│   ├── fonts/        # 字体文件
│   ├── uploads/      # 上传文件存储目录
│   └── index.html    # 主页面
└── README.md         # 项目说明文档
```

## 安装步骤

1. 修改 Nginx 配置（/usr/local/nginx/conf/nginx.conf）：

```nginx
server {
    ...
    client_max_body_size 1024m;  # 允许上传 1GB 文件
    
    ...
}
```

2. 修改 PHP 配置（/usr/local/php/etc/php.ini）：

```ini
upload_max_filesize = 1024M
post_max_size = 1024M
memory_limit = 2048M
max_execution_time = 3600
max_input_time = 3600
```

3. 设置目录权限：

```bash
# 创建上传目录
mkdir -p public/uploads

# 设置权限
chmod 755 public/uploads
chown www-data:www-data public/uploads  # 使用实际的 web 服务器用户
```

4. 重启服务：

```bash
# 重启 Nginx
/etc/init.d/nginx restart

# 重启 PHP-FPM
/etc/init.d/php-fpm restart
```

## 常见问题

1. 413 Request Entity Too Large
   - 需要同时修改 Nginx 和 PHP 的上传限制
   - 修改后必须重启相关服务
   - 检查实际文件大小是否超过限制

2. 权限问题
   - 确保 uploads 目录可写
   - 检查 Web 服务器用户权限
   - 查看错误日志排查问题

3. 上传失败
   - 检查文件类型是否允许
   - 验证文件大小限制
   - 查看 PHP 错误日志（/usr/local/php/var/log/php-error.log）

## 许可证

MIT License
