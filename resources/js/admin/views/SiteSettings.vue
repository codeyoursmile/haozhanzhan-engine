<template>
    <div class="site-settings-container">
        <el-card v-loading="siteStore.loading">
            <template #header>
                <div class="card-header">
                    <span>站点配置</span>
                    <el-button type="primary" @click="handleSubmit" :loading="submitting">
                        保存配置
                    </el-button>
                </div>
            </template>

            <el-form :model="form" :rules="rules" ref="formRef" label-width="120px">
                <el-form-item label="网站名称" prop="site_name">
                    <el-input v-model="form.site_name" placeholder="请输入网站名称" />
                </el-form-item>

                <el-form-item label="网站 Logo" prop="site_logo">
                    <el-input v-model="form.site_logo" placeholder="例如: /logo.png" />
                    <div class="form-tip">Logo 路径，相对于 public 目录</div>
                </el-form-item>

                <el-form-item label="SEO 关键词" prop="site_keywords">
                    <el-input
                        v-model="form.site_keywords"
                        type="textarea"
                        :rows="2"
                        placeholder="多个关键词用英文逗号分隔"
                    />
                </el-form-item>

                <el-form-item label="SEO 描述" prop="site_description">
                    <el-input
                        v-model="form.site_description"
                        type="textarea"
                        :rows="3"
                        placeholder="请输入网站描述"
                    />
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, watch } from 'vue';
import { ElMessage } from 'element-plus';
import { useSiteStore } from '../stores/site';

const siteStore = useSiteStore();
const formRef = ref();
const submitting = ref(false);

const form = reactive({
    site_name: '',
    site_logo: '',
    site_keywords: '',
    site_description: '',
});

const rules = {
    site_name: [{ required: true, message: '请输入网站名称', trigger: 'blur' }],
};

// 监听 store 数据变化，更新表单
watch(
    () => siteStore.site,
    (newSite) => {
        if (newSite) {
            form.site_name = newSite.site_name || '';
            form.site_logo = newSite.site_logo || '';
            form.site_keywords = newSite.site_keywords || '';
            form.site_description = newSite.site_description || '';
        }
    },
    { immediate: true }
);

const handleSubmit = async () => {
    if (!formRef.value) return;

    await formRef.value.validate(async (valid: boolean) => {
        if (!valid) return;

        submitting.value = true;
        try {
            await siteStore.updateSite(form);
        } finally {
            submitting.value = false;
        }
    });
};

onMounted(() => {
    siteStore.fetchSite();
});
</script>

<style scoped>
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.form-tip {
    font-size: 12px;
    color: #909399;
    margin-top: 4px;
}
</style>