<?php if($user): ?>

    Hello, <?=$user->first_name;?>.
    <br><br>
    This is the Doctor. How are you?

<?php else: ?>

    Welcome to my app. Please <a href='/users/signup'>sign up</a> or <a href='/users/login'>log in</a>.

<?php endif; ?>


