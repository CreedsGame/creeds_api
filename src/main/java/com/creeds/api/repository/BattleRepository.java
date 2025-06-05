package com.creeds.api.repository;

import com.creeds.api.model.Battle;
import org.springframework.data.jpa.repository.JpaRepository;
import java.util.List;

public interface BattleRepository extends JpaRepository<Battle, String> {
    List<Battle> findByFighterNameOrOpponentName(String fighterName, String opponentName);
} 