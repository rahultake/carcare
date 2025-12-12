<?php $__env->startSection('title', 'Blogs'); ?>
<?php $__env->startSection('page-title', 'Blogs'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2>Blogs</h2>
    <a href="<?php echo e(route('admin.blogs.create')); ?>" class="btn btn-primary mb-3">Add Blog</a>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Image</th>
                <th>Short Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($blog->title); ?></td>
                    <td><?php echo e($blog->category->name ?? 'N/A'); ?></td>
                    <td>
                        <?php if($blog->image): ?>
                            <img src="<?php echo e(asset($blog->image)); ?>" width="80">
                        <?php endif; ?>
                    </td>
                    <td><?php echo e(Str::limit($blog->short_description, 50)); ?></td>
                    <td>
                        <a href="<?php echo e(route('admin.blogs.edit', $blog)); ?>" class="btn btn-sm btn-warning">Edit</a>
                        <form action="<?php echo e(route('admin.blogs.destroy', $blog)); ?>" method="POST" style="display:inline-block">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5">No blogs found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php echo e($blogs->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u391893744/domains/gitcsdemoserver.online/public_html/carcare/resources/views/admin/blogs/index.blade.php ENDPATH**/ ?>