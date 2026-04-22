<template>
    <div class="editor-container">
        <!-- 左侧：组件库 -->
        <div class="editor-sidebar">
            <div class="sidebar-title">组件库</div>
            <div class="component-list">
                <div class="component-item">横幅 Banner</div>
                <div class="component-item">纯文本</div>
                <div class="component-item">图片</div>
                <div class="component-item">多列布局</div>
                <div class="component-item">地图</div>
                <div class="component-item">联系表单</div>
            </div>
        </div>

        <!-- 中间：画布 -->
        <div class="editor-canvas">
            <div class="canvas-title">页面画布</div>
            <div class="canvas-content">
                <p class="empty-tip">从左侧拖拽组件到这里</p>
            </div>
        </div>

        <!-- 右侧：属性面板 -->
        <div class="editor-property">
            <div class="property-title">属性面板</div>
            <div class="property-content">
                <p class="empty-tip">点击组件编辑属性</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
// 编辑器逻辑后续添加
</script>

<style scoped>
.editor-container {
    display: flex;
    height: calc(100vh - 120px);
    gap: 16px;
}

.editor-sidebar {
    width: 260px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.editor-canvas {
    flex: 1;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.editor-property {
    width: 320px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.sidebar-title,
.canvas-title,
.property-title {
    padding: 16px;
    font-weight: bold;
    border-bottom: 1px solid #e8e8e8;
    background: #fafafa;
}

.component-list {
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.component-item {
    padding: 12px;
    background: #f5f5f5;
    border-radius: 6px;
    cursor: move;
    text-align: center;
    transition: all 0.2s;
}

.component-item:hover {
    background: #e6f7ff;
    border-color: #1890ff;
}

.canvas-content,
.property-content {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
}

.empty-tip {
    text-align: center;
    color: #999;
    padding: 40px;
}
</style>