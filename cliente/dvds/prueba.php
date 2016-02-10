<?php
$oldset = array();
pcntl_sigprocmask(SIG_BLOCK, array(SIGUSR1), $oldset);
print_r ($oldset);
pcntl_sigprocmask(SIG_UNBLOCK, array(SIGUSR1), $oldset);
print_r ($oldset);
?>