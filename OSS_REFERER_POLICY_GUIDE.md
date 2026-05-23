# 阿里云OSS Referer策略配置指南

## 问题说明

如果遇到"You are denied by bucket referer policy"错误，这是因为OSS Bucket配置了防盗链（Referer）策略，拒绝了来自您网站的文件访问请求。

## 解决方案

### 方案一：修改Referer策略（推荐）

#### 步骤1：登录阿里云OSS控制台
1. 访问：https://oss.console.aliyun.com/
2. 选择对应的Bucket（scywp）
3. 进入"权限管理" -> "防盗链设置"

#### 步骤2：配置Referer白名单

**配置选项：**

1. **Referer白名单**：
   ```
   *.monkeycode-ai.online
   localhost
   127.0.0.1
   *.localhost
   ```

2. **允许空Referer**：选择"是"
   - 这对于下载功能很重要

3. **Referer黑名单**：留空（不设置）

#### 步骤3：保存配置

点击"确定"保存配置。

### 方案二：禁用Referer策略（临时方案）

如果不确定如何配置，可以临时禁用Referer策略：

1. 进入"权限管理" -> "防盗链设置"
2. 选择"关闭"
3. 点击"确定"

**注意：** 这样会降低安全性，建议仅在测试时使用。

### 方案三：使用服务器端代理（已实现）

本系统已经实现了服务器端代理下载，通过以下方式避免Referer问题：

- 下载功能：服务器从OSS获取文件，然后传输给用户
- 不依赖浏览器直接访问OSS
- 避免了Referer检查

## 详细配置说明

### Referer白名单格式

```
*.example.com          # 允许example.com的所有子域名
*.monkeycode-ai.online # 允许monkeycode-ai.online的所有子域名
localhost              # 允许本地访问
127.0.0.1             # 允许本地IP访问
```

### 推荐的Referer策略配置

```
状态: 开启
Referer类型: 白名单
Referer字段: 
  *.monkeycode-ai.online
  localhost
  127.0.0.1
允许空Referer: 是
Referer黑名单: (留空)
```

## 验证配置

### 测试文件下载

1. 登录系统
2. 点击任意文件的下载按钮
3. 检查是否能正常下载

### 检查Referer头信息

使用浏览器开发者工具：

1. 按F12打开开发者工具
2. 切换到"Network"标签
3. 点击下载文件
4. 查看请求的Headers，确认Referer信息

## 常见问题

### Q1: 配置了Referer策略还是无法访问

**A:** 请检查：
- Referer白名单是否包含了当前域名
- 是否启用了"允许空Referer"
- 浏览器是否正确发送Referer头

### Q2: 移动端无法下载文件

**A:** 可能的原因：
- 移动端浏览器不发送Referer头
- 需要启用"允许空Referer"选项
- 检查移动端网络设置

### Q3: 如何测试Referer策略

**A:** 使用curl命令测试：

```bash
# 带Referer头的请求
curl -H "Referer: https://monkeycode-ai.online" "OSS文件URL"

# 不带Referer头的请求
curl "OSS文件URL"
```

## 安全建议

### 推荐的安全配置

1. **使用白名单模式**：仅允许特定域名访问
2. **允许空Referer**：确保下载功能正常
3. **定期审查**：检查Referer访问日志
4. **监控异常访问**：发现可疑访问及时处理

### 不推荐的配置

1. **使用黑名单模式**：安全性较低
2. **完全禁用Referer检查**：容易被盗链
3. **过于宽松的白名单**：如使用通配符 `*`

## 监控和日志

### 查看Referer访问日志

1. 进入"日志管理" -> "访问日志"
2. 筛选Referer字段
3. 分析访问模式

### 设置Referer告警

1. 进入"监控管理" -> "告警设置"
2. 配置异常Referer访问告警
3. 设置通知方式

## 其他防盗链设置

### 配置IP白名单（可选）

除了Referer策略，还可以配置IP白名单：

1. 进入"权限管理" -> "IP白名单"
2. 添加允许的IP地址或IP段
3. 设置白名单模式

### 配置访问时间限制（可选）

1. 进入"权限管理" -> "防盗链设置"
2. 配置URL签名有效期
3. 设置访问时间窗口

## 技术细节

### Referer策略工作原理

1. 浏览器向OSS发送请求时，会附带Referer头
2. OSS检查Referer头是否符合配置的策略
3. 如果不符合，拒绝访问并返回错误

### HTTP Referer头示例

```
Referer: https://monkeycode-ai.online:8000/index.php?path=&sort=name&order=asc&page=1
```

### 错误代码说明

- **0003-00000501**：Referer策略拒绝访问
- **AccessDenied**：访问被拒绝
- **You are denied by bucket referer policy**：被Bucket Referer策略拒绝

## 联系支持

如果问题仍然存在，请检查：
- OSS服务状态是否正常
- Bucket配置是否正确
- 网络连接是否稳定
- Referer策略是否正确设置

阿里云技术支持：https://help.aliyun.com/
