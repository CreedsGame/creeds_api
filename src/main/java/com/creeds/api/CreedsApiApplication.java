package com.creeds.api;

import io.swagger.v3.oas.models.OpenAPI;
import io.swagger.v3.oas.models.info.Info;
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.context.annotation.Bean;

@SpringBootApplication
public class CreedsApiApplication {

    public static void main(String[] args) {
        SpringApplication.run(CreedsApiApplication.class, args);
    }

    @Bean
    public OpenAPI creedsApiInfo() {
        return new OpenAPI()
                .info(new Info()
                        .title("Creeds Game API")
                        .description("API for managing characters and battles in the Creeds game")
                        .version("1.0.0"));
    }
} 