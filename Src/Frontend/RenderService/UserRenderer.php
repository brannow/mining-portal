<?php declare(strict_types=1);


namespace Src\Frontend\RenderService;


use Src\Domain\Enum\UserLevel;
use Src\Domain\Model\User;
use Src\Domain\Repository\UserRepository;

abstract class UserRenderer
{
    /**
     * @param User $currentUser
     * @return string
     */
    public static function adminPanel(User $currentUser): string
    {
        return '<div class="double-box">'. static::userList($currentUser) . static::userCreateForm($currentUser) .'<div style="clear: both;"></div></div>';
    }
    
    /**
     * @param User $currentUser
     * @return string
     */
    public static function userList(User $currentUser): string
    {
        
        $userRepo = new UserRepository();
        $users = $userRepo->findAll();
        /** @var User $user */
        $userList = '';
        foreach ($users as $user) {
            
            if ($user->getId() === $currentUser->getId()) {
                continue;
            }
            
            $userList .= '<tr>';
    
            $userList .= '<td>'. $user->getId() .'</td>';
            $userList .= '<td><a href="/user?id='.$user->getId().'">'. htmlentities($user->getUsername()) .'</a></td>';
            $userList .= '<td>'. $user->levelName() .'</td>';
            $userList .= '<td style="text-align: right;"><a href="/user/edit?id='.$user->getId().'">edit</a> <a href="/user/delete?id='.$user->getId().'">delete</a></td>';
    
    
            $userList .= '</tr>';
        }
        
        $listHtml = '<div class="box">
            <div class="header">
                User List
            </div>
            <table style="width: 100%">
               <tr class="nonHover"><th>#</th><th>Username</th><th>Level</th><th></th></tr>
                '. $userList .'
            </table>
        </div>';
        
        return $listHtml;
    }
    
    /**
     * @param User $currentUser
     * @return string
     */
    public static function userCreateForm(User $currentUser): string
    {
        $selectList = '';
        foreach (UserLevel::getConstants() as $constant) {
            if ($constant === UserLevel::ADMIN || $constant === UserLevel::LOCKED) {
                continue;
            }
            $selectList .= '<option value="'.$constant.'">'.UserLevel::getLevelName($constant).'</option>';
        }
        
        return '<div class="box">
            <div class="header">
                Create New User
            </div>
            <form autocomplete="off" action="/user/create" class="action-form" method="post">
                <input type="hidden" name="signature" value="'. md5($currentUser->getId().$currentUser->getUsername()) .'">
                <div>
                    <label for="username">Username</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text" id="username" name="username" />
                </div>
                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" />
                </div>
                <div>
                    <label for="email">E-Mail</label>
                    <input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="email" id="email" name="email" />
                </div>
                <div>
                    <label for="level">Level</label>
                    <select id="level" name="level">
                        '.$selectList.'
                    </select>
                </div>
                <input type="submit" value="Create">
            </form>
        </div>';
    }
    
    /**
     * @param User $currentUser
     * @param User $editUser
     * @return string
     */
    public static function editUserForm(User $currentUser, User $editUser): string
    {
        $userLevel = '';
        if ($currentUser->getLevel() === UserLevel::ADMIN && $currentUser->getId() !== $editUser->getId()) {
            $selectList = '';
            foreach (UserLevel::getConstants() as $constant) {
                $selected = '';
                if ($editUser->getLevel() === $constant) {
                    $selected = 'selected="selected"';
                }
                
                $selectList .= '<option '. $selected .' value="'.$constant.'">'.UserLevel::getLevelName($constant).'</option>';
            }
            $userLevel = '<div>
                <label for="level">Level</label>
                <select id="level" name="level">
                    '.$selectList.'
                </select>
            </div>';
        }
        
        return '<form autocomplete="off" action="/user/editProcess" class="action-form" method="post">
            <input type="hidden" name="signature" value="'. md5($editUser->getId() . '_' . $currentUser->getId().$currentUser->getUsername()) .'">
            <input type="hidden" name="userId" value="'. $editUser->getId() .'">
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" />
            </div>
            <div>
                <label for="email">E-Mail</label>
                <input value="'. htmlentities($editUser->getEmail()) .'" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="email" id="email" name="email" />
            </div>
            '. $userLevel .'
            <input type="submit" value="Edit">
        </form>';
    }
}