<?php
class DB {
    private static function path($file) {
        return __DIR__ . '/../data/' . $file . '.json';
    }

    public static function read($file) {
        $path = self::path($file);
        if (!file_exists($path)) return [];
        $content = file_get_contents($path);
        return json_decode($content, true) ?? [];
    }

    public static function write($file, $data) {
        $path = self::path($file);
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public static function findUser($username) {
        $users = self::read('users');
        foreach ($users as $user) {
            if (strtolower($user['username']) === strtolower($username)) return $user;
        }
        return null;
    }

    public static function findUserById($id) {
        $users = self::read('users');
        foreach ($users as $user) {
            if ($user['id'] === $id) return $user;
        }
        return null;
    }

    public static function createUser($username, $email, $password) {
        $users = self::read('users');
        // Check duplicate
        foreach ($users as $u) {
            if (strtolower($u['username']) === strtolower($username)) return ['error' => 'Username sudah digunakan'];
            if (strtolower($u['email']) === strtolower($email)) return ['error' => 'Email sudah digunakan'];
        }
        $id = 'USR' . str_pad(count($users) + 1, 5, '0', STR_PAD_LEFT);
        $user = [
            'id'         => $id,
            'username'   => $username,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'name'       => $username,
            'avatar'     => '',
            'bio'        => '',
            'created_at' => date('Y-m-d H:i:s'),
            'total_quiz' => 0,
            'total_score'=> 0,
        ];
        $users[] = $user;
        self::write('users', $users);
        return $user;
    }

    public static function updateUser($id, $data) {
        $users = self::read('users');
        foreach ($users as &$user) {
            if ($user['id'] === $id) {
                foreach ($data as $k => $v) {
                    $user[$k] = $v;
                }
                self::write('users', $users);
                return $user;
            }
        }
        return null;
    }

    public static function saveScore($userId, $score, $total, $category) {
        $scores = self::read('scores');
        $scores[] = [
            'id'         => uniqid(),
            'user_id'    => $userId,
            'score'      => $score,
            'total'      => $total,
            'category'   => $category,
            'percentage' => round(($score / $total) * 100),
            'played_at'  => date('Y-m-d H:i:s'),
        ];
        self::write('scores', $scores);

        // Update user stats
        $users = self::read('users');
        foreach ($users as &$user) {
            if ($user['id'] === $userId) {
                $user['total_quiz']++;
                $user['total_score'] += $score;
                break;
            }
        }
        self::write('users', $users);
    }

    public static function getUserScores($userId) {
        $scores = self::read('scores');
        return array_filter($scores, fn($s) => $s['user_id'] === $userId);
    }

    public static function getLeaderboard() {
        $users = self::read('users');
        usort($users, fn($a, $b) => $b['total_score'] - $a['total_score']);
        return array_slice($users, 0, 10);
    }
}
