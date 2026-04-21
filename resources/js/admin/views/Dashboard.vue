<template>
    <div class="dashboard">
        <el-row :gutter="20">
            <el-col :span="6">
                <el-card class="stat-card">
                    <div class="stat-value">{{ pageCount }}</div>
                    <div class="stat-label">页面总数</div>
                </el-card>
            </el-col>
            <el-col :span="6">
                <el-card class="stat-card">
                    <div class="stat-value">0</div>
                    <div class="stat-label">组件总数</div>
                </el-card>
            </el-col>
            <el-col :span="6">
                <el-card class="stat-card">
                    <div class="stat-value">0</div>
                    <div class="stat-label">今日访问</div>
                </el-card>
            </el-col>
            <el-col :span="6">
                <el-card class="stat-card">
                    <div class="stat-value">v1.0</div>
                    <div class="stat-label">系统版本</div>
                </el-card>
            </el-col>
        </el-row>

        <el-card class="welcome-card" style="margin-top: 20px">
            <h3>欢迎使用好站站</h3>
            <p>点击左侧菜单开始建站，或查看使用文档了解更多功能。</p>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';

const pageCount = ref(0);

onMounted(async () => {
    try {
        const response = await axios.get('/api/admin/pages');
        pageCount.value = response.data.length || 0;
    } catch (error) {
        console.error('获取页面统计失败', error);
    }
});
</script>

<style scoped>
.stat-card {
    text-align: center;
}

.stat-value {
    font-size: 32px;
    font-weight: bold;
    color: #1890ff;
}

.stat-label {
    color: #666;
    margin-top: 10px;
}

.welcome-card h3 {
    margin-bottom: 10px;
}

.welcome-card p {
    color: #666;
}
</style>