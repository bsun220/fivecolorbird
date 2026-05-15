-- ============================================
-- 周报表 (hl_weekly_report) 结构变更
-- ============================================

-- 一、新增字段
ALTER TABLE `hl_weekly_report`
ADD COLUMN `start_date` date DEFAULT NULL COMMENT '周起始日期' AFTER `user_id`,
ADD COLUMN `end_date` date DEFAULT NULL COMMENT '周结束日期' AFTER `start_date`,
ADD COLUMN `daily_details` json DEFAULT NULL COMMENT '每日明细JSON' AFTER `end_date`,
ADD COLUMN `total_hours` decimal(10,2) DEFAULT 0 COMMENT '本周合计工时' AFTER `daily_details`,
ADD COLUMN `todo_items` text COMMENT '待办事项' AFTER `total_hours`,
ADD COLUMN `reply` text COMMENT '审批回复' AFTER `todo_items`,
ADD COLUMN `examine_time` int DEFAULT NULL COMMENT '审批时间' AFTER `reply`,
ADD COLUMN `submit_time` int DEFAULT NULL COMMENT '提交时间' AFTER `examine_time`;

-- ============================================
-- 字段变更说明
-- ============================================

-- 新增字段：
--   start_date      - 周起始日期 (date)
--   end_date        - 周结束日期 (date)
--   daily_details   - 每日明细 (JSON格式)
--   total_hours     - 本周合计工时
--   todo_items      - 待办事项
--   reply           - 审批回复
--   examine_time    - 审批时间
--   submit_time     - 提交时间

-- 移除字段（建议数据备份后删除）：
--   file_name        - 周报文件名
--   file_url         - 周报url
--   date            - 年份
--   month           - 第几月
--   week            - 第几周
--   node_id         - 节点ID
--   node            - 节点名称
--   working_hours   - 本周工时
--   actual_hours    - 实际工时
--   unfinished_work_hours - 未完成工时
--   remarks         - 审核备注

-- status 字段含义变更：
--   旧：1=待审核, 2=已审核
--   新：0=未审批, 1=已审批, 2=驳回

-- ============================================
-- 可选：删除旧字段（执行前请先备份数据）
-- ============================================
-- ALTER TABLE `hl_weekly_report`
-- DROP COLUMN IF EXISTS `file_name`,
-- DROP COLUMN IF EXISTS `file_url`,
-- DROP COLUMN IF EXISTS `date`,
-- DROP COLUMN IF EXISTS `month`,
-- DROP COLUMN IF EXISTS `week`,
-- DROP COLUMN IF EXISTS `node_id`,
-- DROP COLUMN IF EXISTS `node`,
-- DROP COLUMN IF EXISTS `working_hours`,
-- DROP COLUMN IF EXISTS `actual_hours`,
-- DROP COLUMN IF EXISTS `unfinished_work_hours`,
-- DROP COLUMN IF EXISTS `remarks`;
