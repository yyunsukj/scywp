# 阿里云OSS访问权限配置指南

## 问题说明

如果遇到"由于桶的引用策略限制，您被拒绝访问"的错误，这是因为OSS Bucket的访问权限配置过于严格。本系统已更新为使用签名URL来访问文件，但为了确保最佳体验，建议按以下方式配置OSS Bucket。

## 解决方案

### 方案一：使用签名URL（推荐，已实现）

本系统已经修改为使用签名URL访问文件，不需要配置复杂的Bucket权限。签名URL有以下优势：

- **安全性高**：每个链接都有有效期和签名
- **权限控制**：可以精确控制访问权限和过期时间
- **灵活配置**：可以根据不同场景设置不同的有效期

### 方案二：配置CORS规则（用于前端预览）

如果需要在浏览器中直接预览文件（如图片、PDF等），需要配置CORS规则：

1. 登录阿里云OSS控制台
2. 找到对应的Bucket（scywp）
3. 进入"权限管理" -> "跨域设置"
4. 点击"创建规则"

**CORS规则配置：**

```
来源: *
允许 Methods: GET, POST, PUT, DELETE, HEAD
允许 Headers: *
暴露 Headers: ETag, x-oss-request-id
缓存时间: 600
```

### 方案三：配置Bucket Policy（可选）

如果需要更高的访问灵活性，可以配置Bucket Policy：

1. 进入"权限管理" -> "Bucket Policy"
2. 点击"创建"
3. 使用以下JSON模板：

```json
{
    "Version": "1",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "oss:GetObject"
            ],
            "Resource": [
                "acs:oss:*:*:scywp/*"
            ],
            "Condition": {
                "IpAddress": {
                    "acs:SourceIp": [
                        "0.0.0.0/0"
                    ]
                }
            }
        }
    ]
}
```

**注意：** 此配置允许公开读取，请根据实际需求调整。

## 验证配置

### 测试签名URL

使用系统自带的测试工具：

```bash
php /tmp/test_signed_url.php
```

### 检查文件访问

1. 登录系统后，点击任意文件
2. 检查是否能够正常访问或下载
3. 查看浏览器开发者工具的网络请求，确认URL格式

## 常见问题

### Q1: 仍然无法访问文件

**A:** 请检查：
- OSS Bucket名称是否正确
- AccessKey ID和Secret是否有效
- 文件是否确实存在于OSS中

### Q2: 签名URL很快过期

**A:** 可以在代码中调整有效期：
- 下载链接：3600秒（1小时）
- 在线预览：86400秒（24小时）

### Q3: CORS错误

**A:** 确保CORS规则配置正确，特别是：
- 来源设置正确
- 允许的Methods包含GET
- 暴露的Headers包含必要的字段

## 推荐配置

**最佳实践配置：**

1. **使用签名URL**（系统默认）
   - 下载链接：1小时有效期
   - 在线预览：24小时有效期

2. **配置CORS规则**
   - 支持浏览器直接预览
   - 允必要的Methods和Headers

3. **保持Bucket私有**
   - 不设置公开读取权限
   - 依赖签名URL进行访问控制

## 技术细节

### 签名URL生成

系统使用阿里云OSS PHP SDK生成签名URL：

```php
$signedUrl = $ossClient->signUrl(OSS_BUCKET, $filePath, $expireTime);
```

### URL参数说明

签名URL包含以下参数：
- `Expires`: URL过期时间戳
- `OSSAccessKeyId`: 访问密钥ID
- `Signature`: 签名验证信息

### 安全建议

1. 不要在前端暴露AccessKey Secret
2. 定期轮换AccessKey
3. 监控OSS访问日志
4. 限制签名URL的有效期

## 联系支持

如果问题仍然存在，请检查：
- 阿里云OSS服务状态
- 网络连接是否正常
- 文件路径是否正确
