# TGFAKA 自动发卡系统

TGFAKA 是一个基于 Laravel 开发的自动发卡系统，主要用于虚拟商品的在线销售和自动发货。
## 前台页面
![front](https://github.com/lc5who/tgfaka/blob/main/img.png)
## 主要功能

- **商品分类管理**
  - 创建/编辑/删除商品分类
  - 分类排序功能
  - 分页获取分类列表

- **订单管理**
  - 创建订单
  - 库存管理
  - 订单状态管理
  - 邮件通知功能

- **支付集成**
  - 支持支付宝支付
  - 支付回调处理
  - 支付状态更新

- **安全特性**
  - 数据库事务处理
  - 库存缓存机制
  - 并发控制锁

## 环境要求

- PHP >= 8.0
- MySQL >= 5.7
- Composer
- Laravel >= 9.0

## 安装部署

1. 克隆仓库
   ```bash
   git clone https://github.com/your-repo/tgfaka.git
   ```

2. 安装依赖
   ```bash
   composer install
   ```

3. 配置环境变量
   复制 `.env.example` 为 `.env` 并配置数据库连接等信息

4. 运行迁移和填充
   ```bash
   php artisan migrate --seed
   ```

5. 启动服务
   ```bash
   php artisan serve
   ```


## 贡献指南

欢迎提交 Pull Request。对于重大更改，请先创建 Issue 讨论变更内容。

## 许可证

[MIT](https://opensource.org/licenses/MIT)
