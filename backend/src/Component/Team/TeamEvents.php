<?php

namespace Component\Team;

final class TeamEvents
{
    const PRE_CREATE = 'app.team.pre_create';

    const POST_CREATE = 'app.team.post_create';

    const BETA_POST_CREATE = 'app.beta_team.post_create';

    const PRE_RESTORE = 'app.team.pre_restore';

    const POST_RESTORE = 'app.team.post_restore';
}
