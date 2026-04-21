<template>
    <el-container class="layout-container">
        <!-- 侧边栏 -->
        <el-aside :width="isCollapse ? '64px' : '220px'" class="aside">
            <div class="logo">
                <span v-if="!isCollapse">好站站</span>
                <span v-else>好</span>
            </div>
            <el-menu
                :collapse="isCollapse"
                router
                :default-active="$route.path"
                background-color="#001529"
                text-color="#bfbfbf"
                active-text-color="#fff"
            >
                <el-menu-item index="/admin">
                    <el-icon><Odometer /></el-icon>
                    <span>仪表盘</span>
                </el-menu-item>
                <el-menu-item index="/admin/pages">
                    <el-icon><Document /></el-icon>
                    <span>页面管理</span>
                </el-menu-item>
                <el-menu-item index="/admin/site">
                    <el-icon><Setting /></el-icon>
                    <span>站点配置</span>
                </el-menu-item>
            </el-menu>
        </el-aside>

        <el-container>
            <el-header class="header">
                <div class="header-left">
                    <el-icon @click="toggleCollapse" class="collapse-btn">
                        <Fold v-if="!isCollapse" />
                        <Expand v-else />
                    </el-icon>
                </div>
                <div class="header-right">
                    <el-dropdown @command="handleCommand">
                        <span class="user-info">
                            {{ authStore.user?.name }}
                            <el-icon><CaretBottom /></el-icon>
                        </span>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item command="logout">退出登录</el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </div>
            </el-header>

            <el-main class="main">
                <router-view />
            </el-main>
        </el-container>
    </el-container>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import { useAuthStore } from '../stores/auth';
import {
    Odometer,
    Document,
    Setting,
    Fold,
    Expand,
    CaretBottom,
} from '@element-plus/icons-vue';

const router = useRouter();
const authStore = useAuthStore();
const isCollapse = ref(false);

const toggleCollapse = () => {
    isCollapse.value = !isCollapse.value;
};

const handleCommand = async (command: string) => {
    if (command === 'logout') {
        await authStore.logout();
        ElMessage.success('已退出登录');
        router.push('/admin/login');
    }
};
</script>

<style scoped>
.layout-container {
    height: 100vh;
}

.aside {
    background-color: #001529;
    transition: width 0.3s;
}

.logo {
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 20px;
    font-weight: bold;
    background-color: #002140;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #fff;
    border-bottom: 1px solid #e8e8e8;
    padding: 0 20px;
}

.collapse-btn {
    font-size: 20px;
    cursor: pointer;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
}

.main {
    background-color: #f0f2f5;
    padding: 20px;
}
</style>