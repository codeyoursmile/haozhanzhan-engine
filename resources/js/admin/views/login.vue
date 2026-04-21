<template>
    <div class="login-container">
        <el-card class="login-card">
            <template #header>
                <div class="login-header">
                    <h2>好站站后台管理</h2>
                    <p>登录开始建站</p>
                </div>
            </template>

            <el-form :model="form" :rules="rules" ref="formRef">
                <el-form-item prop="email">
                    <el-input
                        v-model="form.email"
                        placeholder="邮箱"
                        :prefix-icon="User"
                        size="large"
                    />
                </el-form-item>

                <el-form-item prop="password">
                    <el-input
                        v-model="form.password"
                        type="password"
                        placeholder="密码"
                        :prefix-icon="Lock"
                        size="large"
                        show-password
                    />
                </el-form-item>

                <el-form-item>
                    <el-button
                        type="primary"
                        size="large"
                        :loading="loading"
                        @click="handleLogin"
                        style="width: 100%"
                    >
                        登录
                    </el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import { User, Lock } from '@element-plus/icons-vue';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const formRef = ref();
const loading = ref(false);

const form = reactive({
    email: '',
    password: '',
});

const rules = {
    email: [
        { required: true, message: '请输入邮箱', trigger: 'blur' },
        { type: 'email' as const, message: '邮箱格式不正确', trigger: 'blur' },
    ],
    password: [
        { required: true, message: '请输入密码', trigger: 'blur' },
        { min: 6, message: '密码至少6位', trigger: 'blur' },
    ],
};

const handleLogin = async () => {
    if (!formRef.value) return;

    await formRef.value.validate(async (valid: boolean) => {
        if (!valid) return;

        loading.value = true;
        const success = await authStore.login(form.email, form.password);
        loading.value = false;

        if (success) {
            ElMessage.success('登录成功');
            router.push('/admin');
        } else {
            ElMessage.error('邮箱或密码错误');
        }
    });
};
</script>

<style scoped>
.login-container {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.login-card {
    width: 400px;
}

.login-header {
    text-align: center;
}
</style>