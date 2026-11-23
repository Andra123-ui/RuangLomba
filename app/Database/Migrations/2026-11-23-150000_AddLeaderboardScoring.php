<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLeaderboardScoring extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('registrations', [
            'placement' => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
                'after'    => 'verified_at',
            ],
        ]);

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'registration_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'award_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'points' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['registration_id', 'user_id', 'award_type']);
        $this->forge->addKey('user_id');
        $this->forge->createTable('registration_point_awards');

        $this->db->query(
            "INSERT INTO registration_point_awards (registration_id, user_id, award_type, points)
             SELECT r.id, r.user_id, 'participation', 50
             FROM registrations r
             JOIN competitions c ON c.id = r.competition_id
             WHERE r.status = 'verified' AND c.type <> 'Lomba'"
        );
    }

    public function down(): void
    {
        $this->forge->dropTable('registration_point_awards', true);
        $this->forge->dropColumn('registrations', 'placement');
    }
}
