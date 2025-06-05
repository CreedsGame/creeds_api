package com.creeds.api.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Pattern;
import jakarta.validation.constraints.Size;
import lombok.Data;
import lombok.NoArgsConstructor;

@Data
@NoArgsConstructor
@Entity
@Table(name = "characters")
public class Character {
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    
    @NotBlank
    @Size(max = 30)
    @Pattern(regexp = "^[a-zA-Z0-9_]*$", message = "Character name must be alphanumeric")
    @Column(unique = true, nullable = false)
    private String name;
    
    @NotBlank
    @Size(max = 50)
    @Column(nullable = false)
    private String password;
    
    @Column(nullable = false)
    private Integer level = 1;
    
    @Column(nullable = false)
    private Integer strength = 10;
    
    @Column(nullable = false)
    private Integer endurance = 10;
    
    @Column(nullable = false)
    private Integer initiative = 10;
    
    @Column(nullable = false)
    private Integer life;
    
    @PrePersist
    @PreUpdate
    public void calculateLife() {
        this.life = calculateLifePoints(this.level);
    }
    
    private int calculateLifePoints(int level) {
        return 100 + (level * 10);
    }
} 