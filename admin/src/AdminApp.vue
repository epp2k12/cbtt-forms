<template> 
    <header>
        <div class="wrapper">
            <h1>{{ name }}</h1>
        </div>
    </header>

    <RouterView />
</template>
<script setup> 
    const name = ref('Test Name')
    onMounted( () => {
       console.log("the name : ", name.value)
    })
    
</script>
